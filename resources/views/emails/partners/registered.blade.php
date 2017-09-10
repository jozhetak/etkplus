@component('mail::message')
Добро пожаловать в систему <b>ЕТКплюс</b>!
Теперь сообщество пользователей транспортных карт узнает о Вас!
<hr>
Ваш логин для входа в систему: {{ $email }}
Ваш пароль: {{ $password }}
Никому не сообщайте эти данные.
Это письмо было сгенерировано автоматически, на него не нужно отвечать.
@component('mail::button', ['url' => 'http://etkplus-beta.ru/dashboard'])
Перейти в панель управления
@endcomponent

Благодарим за сотрудничество!<br>
{{ config('app.name') }}
@endcomponent
