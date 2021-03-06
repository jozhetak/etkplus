<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Hash;
use \App\User;
use Carbon\Carbon;
class APIController extends Controller
{
    protected function modifyToShortNumber($num){
        return substr_replace($num, '', 1,1);
    }

    protected function modifyToFullNumber($number){
      $card_num_part2 = substr($number,1,2);
      $card_num_part3  = substr($number,3,6);
      if ($card_num_part2 !== 99){ $prefix = '01'; } else {$prefix = '02';}
      $full_card_number = $prefix . $card_num_part2 . $card_num_part3;
      return $full_card_number;
    }
    /**
     * АУТЕНТИФИКАЦИЯ
     */
    public function postLogin(Request $request){
        $username = $request->username;
        $password = $request->password;
        $token = $request->token;
        /**
         * ПРОВЕРКА ТОКЕНА
         */
        if ($token !== env('API_TOKEN')){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Ошибка взаимодействия'
            ],200);
        }
        /**
         * ПРОВЕРКА ЗАПОЛНЕННОСТИ ПОЛЕЙ
         */
        if (($username == '') || ($password == '')){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Заполнены не все поля'
            ],200);            
        }
        /**
         * ПРОВЕРКА СУЩЕСТВОВАНИЯ ПОЛЬЗОВАТЕЛЯ
         */
        if (($user = DB::table('users')
                ->where('email',$username)
                ->first()) == NULL){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Такого пользователя не существует'
            ],200);
        }
        /**
         * АУТЕНТИФИКАЦИЯ
         */
        
        if (($username !== $user->email) || !(Hash::check($password,$user->password))){
            return response()->json([
                'status' => 'error',
                'errorText' => 'E-mail или пароль не совпадают'
            ],200);        
        }
        /**
         * АВТОРИЗАЦИЯ
         */
        if (($user->role_id < 20) || ($user->role_id > 25)){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Недостаточно прав'
            ],200); 
        }

        /**
         * ПОИСК ОРГАНИЗАЦИИ
         */
        if (($partner = DB::table('ETKPLUS_PARTNERS')
                       ->where('id',$user->partner_id)
                       ->first()) == NULL){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Не удается найти организацию'
            ],200);
        }
        /**
         * ПРОВЕРКА БАЛАНСА
         */
        if (($balance_account = DB::table('ETKPLUS_PARTNER_ACCOUNTS')
                       ->where('partner_id',$partner->id)
                       ->first()) == NULL){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Не удается найти данные по балансу'
            ],200);
        } else {
            $balance = $balance_account->value;
            $minBalance = $balance_account->min_value;
        }
        /**
         * ЗАГРУЗКА ПОСЛЕДНИХ ОПЕРАЦИЙ
         */
        $operations = DB::table('ETKPLUS_VISITS')
                       ->where('partner_id',$partner->id)
                       ->where('operator_id',$user->id)
                       ->limit(5)
                       ->orderBy('created_at','desc')
                       ->get();
        /**
         * ЗАГРУЗКА СКИДОК
         */
        $discounts = DB::table('ETKPLUS_PARTNER_DISCOUNTS')
                        ->where('partner_id',$partner->id)
                        ->get();    
        /**
         * ЗАГРУЗКА БОНУСОВ
         */
        $bonuses = DB::table('ETKPLUS_PARTNER_BONUSES')
                        ->where('partner_id',$partner->id)
                        ->get();    
        /**
         * ТИП ОПЕРАТОРА
         * 1 - С ИНТЕГРАЦИЕЙ С 1С
         * 2 - БЕЗ ИНТЕГРАЦИИ
         */
        $operator = DB::table('ETKPLUS_PARTNER_OPERATORS')
                      ->where('user_id',$user->id)
                      ->first();
        $operatorType = $operator->type;
        $host_address_1c = $operator->host_address_1c;
        /**
         * ОТПРАВКА ОТВЕТА
         */
        return response()->json([
            'status'     => 'success',
            'user'       => $user,
            'partner'    => $partner,
            'balance'    => $balance,
            'minBalance' => $minBalance,
            'operations' => $operations,
            'discounts'  => $discounts,
            'bonuses'    => $bonuses,
            'operatorType' => $operatorType,
            'hostAddress1c' => $host_address_1c 
        ],200);
    }

    public function postGetCard(Request $request){
        $chip       = $request->chip . '000000000000';
        $partner_id = $request->partner_id;
        $token      = $request->token;

        /**
         * ПРОВЕРКА ТОКЕНА
         */
        if ($token !== env('API_TOKEN')){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Ошибка взаимодействия'
            ],200);
        }
        /**
         * ПРОВЕРКА СУЩЕСТВОВАНИЯ КАРТЫ И ФОРМАТИРОВАНИЕ НОМЕРА КАРТЫ
         */
        if (($card = DB::table('ETK_CARDS')
                     ->where('chip', $chip)
                     ->first()) == NULL){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Такой карты не существует'
            ],200);  
        } else $cardNumber = $this->modifyToShortNumber($card->num);          
        /**
         * ПОИСК БОНУСОВ
         */
        if (($cardBonusesRow = DB::table('ETKPLUS_PARTNER_USER_BONUSES')
                        ->where('partner_id',$partner_id)
                        ->where('card_number',$cardNumber)
                        ->first()) == NULL){
            $cardBonuses = 0;
        } else $cardBonuses = $cardBonusesRow->value;
        /**
         * КОЛИЧЕСТВО ПОСЕЩЕНИЙ
         */
        $cardVisitCount = DB::table('ETKPLUS_VISITS')
                    ->where('card_number',$cardNumber)
                    ->where('partner_id', $partner_id)
                    ->count();
        /**
         * СУММА СЧЕТОВ ПОСЕЩЕНИЙ
         */
        $cardVisitSummary = DB::table('ETKPLUS_VISITS')
                    ->where('card_number',$cardNumber)
                    ->where('partner_id', $partner_id)
                    ->sum('ETKPLUS_VISITS.bill');
        /**
         * ПРОВЕРКА СКИДКИ НА ФИКСИРОВАННОСТЬ
         */
        $partner = DB::table('ETKPLUS_PARTNERS')
                    ->where('id',$partner_id)
                    ->first();
        $discountIsFixed = $partner->default_discount;
        /**
         * ПОВЕРКА БОНУСА НА ФИКСИРОВАННОСТЬ
         */
        $bonusIsFixed = $partner->default_bonus;
        /**
         * ОТПРАВКА ОТВЕТА
         */
        return response()->json([
            'status'            => 'success',
            'cardNumber'        => $cardNumber,
            'cardBonuses'       => $cardBonuses,
            'cardVisitCount'    => $cardVisitCount,
            'cardVisitSummary'  => $cardVisitSummary,
            'discountIsFixed'   => $discountIsFixed,
            'bonusIsBonus'      => $bonusIsFixed
        ],200);
    }

    public function postCreateOperation(Request $request){
        /**
         * ПАРАМЕТРЫ ПО УМОЛЧАНИЮ
         */
        $partner_id  = $request->partner_id;
        $card_number = $request->card_number;
        $operator_id = $request->operator_id;
        $token       = $request->token;
        /**
         * ВВОДИМЫЕ ПАРАМЕТРЫ
         */
        $bill        = $request->bill;
        $discount    = $request->discount;
        $bonus       = $request->bonus;
        $sub_bonus   = $request->sub_bonus;
        /**
         * ПРОВЕРКА ТОКЕНА
         */
       /* if ($token !== env('API_TOKEN')){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Ошибка взаимодействия'
            ],200);
        }
        /**
         * ПРОВЕРКА ВХОДНЫХ ПАРАМЕТРОВ
         */
        /**
         * ПРОВЕРКА КАРТЫ
         */
        try {
        $card = DB::table('ETK_CARDS')
            ->where('num',$this->modifyToFullNumber($card_number))
            ->first();
          $card_chip = $card->chip; 
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'errorText' => 'Ошибка при запросе номера карты'
            ],200);   
        }
        /**
         * ПРОВЕРКА СЧЕТА
         */
        if ($bill == ''){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Не введена сумма счета'
            ],200);            
        }
        if ($bill <= 0){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Сумма счета не может быть отрицательной либо равной нулю'
            ],200);            
        } 
        if (!is_numeric($bill)){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Сумма счета должна быть числом'
            ],200);            
        }  
        /**
         * ПРОВЕРКА СКИДКИ
         */
        if ($discount == ''){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Необходимо ввести значение скидки'
            ],200);            
        }
        if ($discount <= 0){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Скидка не может быть отрицательной либо равной нулю'
            ],200);            
        } 
        if ($discount > 90){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Скидка не может быть больше 90%'
            ],200);            
        } 
        if (!is_numeric($discount)){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Введенное значение скидки не является числом'
            ],200);            
        } 
        /**
         * ПРОВЕРКА БОНУСА
         */
        if ($bonus == '') $bonus = 0;
        if ($bonus < 0){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Размер бонуса не может быть отрицательным'
            ],200);            
        } 
        if (!is_numeric($bonus)){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Введенное значение бонуса не является числом'
            ],200);            
        }
        /**
         * ПРОВЕРКА СПИСАНИЯ БОНУСА
         */
        if ($sub_bonus == '') $sub_bonus = 0;
        if ($sub_bonus < 0){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Размер списания бонуса не может быть отрицательным'
            ],200);            
        } 
        if (!is_numeric($sub_bonus)){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Введенное значение списания бонуса не является числом'
            ],200);            
        }
        if (($cardBonusesRow = DB::table('ETKPLUS_PARTNER_USER_BONUSES')
                        ->where('partner_id',$partner_id)
                        ->where('card_number',$card_number)
                        ->first()) == NULL){
          DB::table('ETKPLUS_PARTNER_USER_BONUSES')
            ->insert([
              'partner_id' => $partner_id,
              'card_number' => $card_number,
              'value' => 0
            ]);
        } else if ($sub_bonus > $cardBonusesRow->value){
            return response()->json([
                'status' => 'error',
                'errorText' => 'На карте недостаточно бонусов для списания'
            ],200);       
        }
      /**
       * ПРОВЕРКА БАЛАНСА, ДОСТУПНОГО ДЛЯ ПРОВЕДЕНИЯ ОПЕРАЦИИ
       */
      $balance_row = DB::table('ETKPLUS_PARTNER_ACCOUNTS')
                            ->where('partner_id',$partner_id)
                            ->first();

      $balance = $balance_row->value;
      $balance_min = $balance_row->min_value;
      /**
       * РАСЧЕТ ОПЕРАЦИИ
       */
      $discount_value = ($bill*($discount/100));
      $bill_with_discount = (($bill - $discount_value) - $sub_bonus);
      /**
       * РАСЧЕТ НОВОГО ЗНАЧЕНИЯ БОНУСА
       */
      $user_bonuses = DB::table('ETKPLUS_PARTNER_USER_BONUSES')
        ->where('partner_id', $partner_id)
        ->where('card_number',$card_number)
        ->first();
      $new_user_bonus_value = ($user_bonuses->value + $bonus - $sub_bonus);
      /**
       * НОМЕР КАРТЫ ПО ФОРМАТУ В
       */
      $b_card_number = $this->modifyToFullNumber($card_number);
      /**
       * ДОСТАТОЧНО ЛИ СРЕДСТВ НА АККАУНТЕ
       */
      $partner = DB::table('ETKPLUS_PARTNERS')
                    ->where('id',$partner_id)
                    ->first();
      $tariff = DB::table('ETKPLUS_TARIFFS')
                  ->where('id',$partner->tariff_id)
                  ->first();
      /**
       * РАСЧЕТ КЭШБЭКА
       */
      $partner = \App\Partner::find($partner_id);
      /**
       * LIFETIME КЭШБЭКА
       */
      $cashback_lifetime = Carbon::now();
      $cashback_lifetime->addYear();
      /**
       * ЗНАЧЕНИЕ КЭШБЭКА ДЛЯ ЗАЧИСЛЕНИЯ НА КАРТУ
       */
      $cashback = ceil(($bill*($tariff->cashback/100))); //ОКРУГЛЯЕМ КЭШБЭК В БОЛЬШУЮ СТОРОНУ
      if (($balance - ($bill*$tariff->comission/100)) < $balance_min ){
        return response()->json([
            'status' => 'error',
            'errorText' => 'Недостаточно средств для проведения операции'
        ],200);
      } else {
        $comission = ($bill*$tariff->comission/100);
        $new_balance = ($balance - $comission);
      }

      /**
       * ПРОВЕДЕНИЕ ОПЕРАЦИИ
       */
      try {
        DB::transaction(function() use ($partner_id,$operator_id,$card_number,$card_chip,$bill,$bill_with_discount,$bonus,$sub_bonus,$discount,$discount_value,$comission,$cashback,$new_user_bonus_value,$new_balance,$b_card_number,$cashback_lifetime) {
          DB::table('ETKPLUS_VISITS')
            ->insert([
              'partner_id' => $partner_id,
              'operator_id' => $operator_id,
              'card_number' => $card_number,
              'card_chip' => $card_chip,
              'bill' => $bill,
              'bill_with_discount' => $bill_with_discount,
              'bonus' => $bonus,
              'sub_bonus' => $sub_bonus,
              'discount' => $discount,
              'discount_value' => $discount_value,
              'comission' => $comission,
              'cashback' => $cashback   
            ]);
          DB::table('ETKPLUS_PARTNER_USER_BONUSES')
            ->where('card_number',$card_number)
            ->where('partner_id', $partner_id)
            ->update([
              'value' => $new_user_bonus_value
            ]);
          DB::table('ETKPLUS_PARTNER_ACCOUNTS')
            ->where('partner_id',$partner_id)
            ->update([
                'value' => $new_balance
            ]);
            /**
             * КЭШБЭК
             */
          DB::table('ETK_CARDS')
            ->where('num',$b_card_number)
            ->update(['cashback_to_pay' => $cashback]);
          DB::table('ETKPLUS_CASHBACKS')
              ->insert([
                'card_number' => $card_number,
                'value' => $cashback,
                'status' => 1,
                'lifetime' => $cashback_lifetime
              ]);
        }); 
      } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'errorText' => 'Что-то пошло не так. Попробуйте повторить попытку'
        ],200);
      }
      /**
       * ОТВЕТ ОБ УСПЕШНОЙ ОПЕРАЦИИ
       */
        return response()->json([
            'status' => 'success',
            'text' => 'Операция успешно проведена!'
        ],200);

    }

    public function postCreateOperation1C(Request $request){
        /**
         * ПАРАМЕТРЫ ПО УМОЛЧАНИЮ
         */
        $partner_id  = $request->partner_id;
        $card_number = $request->card_number;
        $operator_id = $request->operator_id;
        $token       = $request->token;
        /**
         * ВВОДИМЫЕ ПАРАМЕТРЫ
         */
        $bill        = $request->bill;
        $sub_bonus   = $request->sub_bonus;
        /**
         * ПРОВЕРКА ТОКЕНА
         */
       /* if ($token !== env('API_TOKEN')){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Ошибка взаимодействия'
            ],200);
        }
        /**
         * ПРОВЕРКА ВХОДНЫХ ПАРАМЕТРОВ
         */
        /**
         * ПРОВЕРКА КАРТЫ
         */
        try {
        $card = DB::table('ETK_CARDS')
            ->where('num',$this->modifyToFullNumber($card_number))
            ->first();
          $card_chip = $card->chip; 
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'errorText' => 'Ошибка при запросе номера карты'
            ],200);   
        }
        /**
         * ПРОВЕРКА СЧЕТА
         */
        if ($bill == ''){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Не введена сумма счета'
            ],200);            
        }
        if ($bill <= 0){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Сумма счета не может быть отрицательной либо равной нулю'
            ],200);            
        } 
        if (!is_numeric($bill)){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Сумма счета должна быть числом'
            ],200);            
        }  
        /**
         * ПРОВЕРКА СПИСАНИЯ БОНУСА
         */
        if ($sub_bonus == '') $sub_bonus = 0;
        if ($sub_bonus < 0){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Размер списания бонуса не может быть отрицательным'
            ],200);            
        } 
        if (!is_numeric($sub_bonus)){
            return response()->json([
                'status' => 'error',
                'errorText' => 'Введенное значение списания бонуса не является числом'
            ],200);            
        }
        if (($cardBonusesRow = DB::table('ETKPLUS_PARTNER_USER_BONUSES')
                        ->where('partner_id',$partner_id)
                        ->where('card_number',$card_number)
                        ->first()) == NULL){
          DB::table('ETKPLUS_PARTNER_USER_BONUSES')
            ->insert([
              'partner_id' => $partner_id,
              'card_number' => $card_number,
              'value' => 0
            ]);
        } else if ($sub_bonus > $cardBonusesRow->value){
            return response()->json([
                'status' => 'error',
                'errorText' => 'На карте недостаточно бонусов для списания'
            ],200);       
        }
      /**
       * ПРОВЕРКА БАЛАНСА, ДОСТУПНОГО ДЛЯ ПРОВЕДЕНИЯ ОПЕРАЦИИ
       */
      $balance_row = DB::table('ETKPLUS_PARTNER_ACCOUNTS')
                            ->where('partner_id',$partner_id)
                            ->first();

      $balance = $balance_row->value;
      $balance_min = $balance_row->min_value;
      $partner = \App\Partner::find($partner_id);
      /**
       * РАСЧЕТ ОПЕРАЦИИ
       */
      /**
       * ПОЛУЧЕНИЕ ЗНАЧЕНИЯ СКИДКИ И ФИКСИРОВАННОГО БОНУСА
       */
      $discout = $partner->default_discount;
      $bonus_in_percent  = $partner->default_bonus;

      $discount_value = ($bill*($discount/100));
      $bill_with_discount = (($bill - $discount_value) - $sub_bonus);

      $bonus = ($bill_with_discount*($bonus_in_percent/100));
      /**
       * РАСЧЕТ НОВОГО ЗНАЧЕНИЯ БОНУСА
       */
      $user_bonuses = DB::table('ETKPLUS_PARTNER_USER_BONUSES')
        ->where('partner_id', $partner_id)
        ->where('card_number',$card_number)
        ->first();
      $new_user_bonus_value = ($user_bonuses->value + $bonus - $sub_bonus);
      /**
       * НОМЕР КАРТЫ ПО ФОРМАТУ В
       */
      $b_card_number = $this->modifyToFullNumber($card_number);
      /**
       * ДОСТАТОЧНО ЛИ СРЕДСТВ НА АККАУНТЕ
       */
      $tariff = DB::table('ETKPLUS_TARIFFS')
                  ->where('id',$partner->tariff_id)
                  ->first();
      /**
       * РАСЧЕТ КЭШБЭКА
       */
      /**
       * LIFETIME КЭШБЭКА
       */
      $cashback_lifetime = Carbon::now();
      $cashback_lifetime->addYear();
      /**
       * ЗНАЧЕНИЕ КЭШБЭКА ДЛЯ ЗАЧИСЛЕНИЯ НА КАРТУ
       */
      $cashback = ceil(($bill*($tariff->cashback/100))); //ОКРУГЛЯЕМ КЭШБЭК В БОЛЬШУЮ СТОРОНУ
      if (($balance - ($bill*$tariff->comission/100)) < $balance_min ){
        return response()->json([
            'status' => 'error',
            'errorText' => 'Недостаточно средств для проведения операции'
        ],200);
      } else {
        $comission = ($bill*$tariff->comission/100);
        $new_balance = ($balance - $comission);
      }
      

      /**
       * ПРОВЕДЕНИЕ ОПЕРАЦИИ
       */
      try {
        DB::transaction(function() use ($partner_id,$operator_id,$card_number,$card_chip,$bill,$bill_with_discount,$bonus,$sub_bonus,$discount,$discount_value,$comission,$cashback,$new_user_bonus_value,$new_balance,$b_card_number,$cashback_lifetime) {
          DB::table('ETKPLUS_VISITS')
            ->insert([
              'partner_id' => $partner_id,
              'operator_id' => $operator_id,
              'card_number' => $card_number,
              'card_chip' => $card_chip,
              'bill' => $bill,
              'bill_with_discount' => $bill_with_discount,
              'bonus' => $bonus,
              'sub_bonus' => $sub_bonus,
              'discount' => $discount,
              'discount_value' => $discount_value,
              'comission' => $comission,
              'cashback' => $cashback   
            ]);
          DB::table('ETKPLUS_PARTNER_USER_BONUSES')
            ->where('card_number',$card_number)
            ->where('partner_id', $partner_id)
            ->update([
              'value' => $new_user_bonus_value
            ]);
          DB::table('ETKPLUS_PARTNER_ACCOUNTS')
            ->where('partner_id',$partner_id)
            ->update([
                'value' => $new_balance
            ]);
            /**
             * КЭШБЭК
             */
          DB::table('ETK_CARDS')
            ->where('num',$b_card_number)
            ->update(['cashback_to_pay' => $cashback]);
          DB::table('ETKPLUS_CASHBACKS')
              ->insert([
                'card_number' => $card_number,
                'value' => $cashback,
                'status' => 1,
                'lifetime' => $cashback_lifetime
              ]);
        }); 
      } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'errorText' => 'Что-то пошло не так. Попробуйте повторить попытку'
        ],200);
      }
      /**
       * ОТВЕТ ОБ УСПЕШНОЙ ОПЕРАЦИИ
       */
        return response()->json([
            'status' => 'success',
            'text' => 'Операция успешно проведена!'
        ],200);

    }
}
