<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Mail;
use \App\User;
use \App\Partner;
use App\Mail\PartnerRegistered;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


class AdminController extends Controller
{
    public function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

    public function showDashboard(){
    	return view('dashboard');
    }
    public function showCreatePartnerPage(){
    	$categories = DB::table('ETKPLUS_PARTNER_CATEGORIES')
    					->get();
    	return view('dashboard.create_partner',[
    		'categories' => $categories
    		]);
    }
    /**
     * СОЗДАНИЕ ПРЕДПРИЯТИЯ
     */
    public function postCreatePartner(Request $request){
    	/**
    	 * DEFAULTS
    	 */
    	$background_imagename = '/assets/img/partners/etkplus_partner_default.jpg';
    	$logo_imagename = '/assets/img/partners/etkplus_partner_default_logo.png';
    	/**
    	 * GET VARIABLES
    	 */
        $user_id     = $request->user_id;
    	$name 		 = $request->name;
    	$fullname    = $request->fullname;
    	$description = $request->description;
    	$phone 		 = $request->phone;
    	$address 	 = $request->phone;
    	$email 		 = $request->email;
    	$site 		 = $request->site;
    	$comission   = $request->comission;
    	$discount    = $request->discount;
    	$category    = $request->category;
    	$is_active   = $request->is_active;
    	if ($is_active == 'on'){
    		$is_active = 1;
    	} else $is_active = 0;
    	/**
    	 * INSERT ROW
    	 */
    	$partnerId = DB::table('ETKPLUS_PARTNERS')->insertGetId([
    		'name' => $name,
    		'fullname' => $fullname,
    		'description' => $description,
    		'phone' => $phone,
    		'address' => $address,
    		'email' => $email,
    		'site' => $site,
    		'default_comission' => $comission,
    		'default_discount' => $discount,
    		'category' => $category,
    		'is_active' => $is_active,
            'created_by' => $user_id
    		]);
    	$partner = \App\Partner::find($partnerId);
        $partner->contract_id = date('y') . '-' . $partner->id;
    	/**
    	 * CHECK FILES
    	 */
    	$background_image = $request->file('background_image');
        if ($background_image){
          $background_image_extension = $request->file('background_image')->getClientOriginalExtension();
          $background_imagename = '/assets/img/partners/' . $partnerId . '/background.' . $background_image_extension;        	
          Storage::disk('public')->put($background_imagename, File::get($background_image));
          $partner->thumbnail = $background_imagename;
          $partner->save();
        } else {
          $partner->thumbnail = $background_imagename;
          $partner->save();
        }

        $logo_image = $request->file('logo_image');
        if ($logo_image){
          $logo_image_extension = $request->file('logo_image')->getClientOriginalExtension();	
          $logo_imagename = '/assets/img/partners/' . $partnerId . '/logo.' . $logo_image_extension;        	
          Storage::disk('public')->put($logo_imagename, File::get($logo_image));
          $partner->logo = $logo_imagename;
          $partner->save();
        } else {
          $partner->logo = $logo_imagename;
          $partner->save();
        }
        /**
         * CREATE USER
         * @var [type]
         */
        $user = new \App\User;
        $user->username = $email;
        $user->email = $email;
        $user->name = $name;
        /**
         * GENERATE PASSWORD
         */
        $password = $this->generateRandomString(); //TO SEND VIA EMAIL
        $encrypted_password = bcrypt($password);
        /**
         * 
         */
        $user->password = $encrypted_password;
        $user->phone = $phone;
        $user->profile_image = $partner->logo;
        $user->role_id = 21;
        $user->is_active = 1;
        $user->save();
        /**
         * SEND EMAIL
         */
        try {
            Mail::to($email)->send(new PartnerRegistered($email,$password));
        } catch (Exception $e) {
            Session::flash('error', $e);
            return redirect()->back();
        }
        /**
         * USER CREATED
         */
                    Session::flash('success', 'Создано новое предприятие');
            return redirect()->back();
    }

    public function getPartnerList(){
        $partners = DB::table('ETKPLUS_PARTNERS')
                        ->join('ETKPLUS_PARTNER_CATEGORIES','ETKPLUS_PARTNERS.category','=','ETKPLUS_PARTNER_CATEGORIES.id')
                        ->select('ETKPLUS_PARTNERS.*','ETKPLUS_PARTNER_CATEGORIES.name as category_name','ETKPLUS_PARTNER_CATEGORIES.id as category_id')
                        ->paginate(20);
        $addresses = DB::table('ETKPLUS_ADDRESSES')
                       ->get();
        $gallery_items = DB::table('ETKPLUS_PARTNER_PHOTOS')
                            ->get();
        return view('dashboard.partner_list',[
            'partners' => $partners,
            'gallery_items' => $gallery_items,
            'addresses' => $addresses
            ]);
    }
/**
 * [postDeletePartner description]
 * @param  Request $request [description]
 * @return [type]           [description]
 */
    public function postDeletePartner(Request $request){
        $user_id = $request->user_id;
        $partner_id = $request->partner_id;
        try {
          DB::table('ETKPLUS_PARTNERS')
            ->where('id', $partner_id)
            ->delete();   
        } catch (Exception $e) {
            Session::flash('error',$e);
            return redirect()->back();
        }
        try {
            $dir_path = '/assets/img/partners/' . $partner_id;
            Storage::disk('public')->deleteDirectory($dir_path);   
        } catch (Exception $e) {
            Session::flash('error',$e);
            return redirect()->back();      
        }
        try {
            DB::table('ETKPLUS_ADDRESSES')
              ->where('partner_id',$partner_id)
              ->delete();
        } catch (Exception $e) {
            Session::flash('error',$e);
            return redirect()->back();    
        }
        Session::flash('success','Предприятие успешно удалено');
        return redirect()->back();    
    }

    public function postEditPartner(Request $request){
        /**
         * GET VARIABLES
         */
        $partner_id  = $request->partner_id;
        $user_id     = $request->user_id;
        $name        = $request->name;
        $fullname    = $request->fullname;
        $description = $request->description;
        $phone       = $request->phone;
        $address     = $request->phone;
        $email       = $request->email;
        $site        = $request->site;
        $comission   = $request->comission;
        $discount    = $request->discount;
        $category    = $request->category;
        $is_active   = $request->is_active;
        if ($is_active == 'on'){
            $is_active = 1;
        } else $is_active = 0;
        /**
         * SAVE CHANGES
         */
        try {
        DB::table('ETKPLUS_PARTNERS')
          ->where('id',$partner_id)
          ->update([
            'name' => $name,
            'fullname' => $fullname,
            'description' => $description,
            'phone' => $phone,
            'address' => $address,
            'email' => $email,
            'site' => $site,
            'default_comission' => $comission,
            'default_discount' => $discount,
            'category' => $category,
            'is_active' => $is_active,
            'updated_by' => $user_id
            ]);   
        } catch (Exception $e) {
            Session::flash('error',$e);
            return redirect()->back();  
        }
        Session::flash('success','Данные организации успешно изменены');
        return redirect()->back();   


    }
    /**
     * [postEditPartnerLogos description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function postEditPartnerLogos(Request $request){
        $partner_id  = $request->partner_id;
        $user_id     = $request->user_id;

        $partner = \App\Partner::find($partner_id);

        $background_image = $request->file('background_image');
        if ($background_image){
          $background_image_extension = $request->file('background_image')->getClientOriginalExtension();
          $background_imagename = '/assets/img/partners/' . $partner->id . '/background.' . $background_image_extension;          
          Storage::disk('public')->put($background_imagename, File::get($background_image));
          $partner->thumbnail = $background_imagename;
          $partner->save();
        } 

        $logo_image = $request->file('logo_image');
        if ($logo_image){
          $logo_image_extension = $request->file('logo_image')->getClientOriginalExtension();   
          $logo_imagename = '/assets/img/partners/' . $partner->id . '/logo.' . $logo_image_extension;            
          Storage::disk('public')->put($logo_imagename, File::get($logo_image));
          $partner->logo = $logo_imagename;
          $partner->save();
        }
        Session::flash('success', 'Изменения сохранены');
        return redirect()->back();
    }

/**
 * GALLERY ITEMS
 *
 *
 * 
 * @param  Request $request [description]
 * @return [type]           [description]
 */
    public function postEditGalleryItem(Request $request){
        $gallery_item_id = $request->gallery_item_id;
        $image_caption = $request->image_caption;
        DB::table('ETKPLUS_PARTNER_PHOTOS')
          ->where('id', $gallery_item_id)
          ->update(['image_caption' => $image_caption]);
        Session::flash('success','Название элемента галереи успешно изменено');
        return redirect()->back();
    }
    public function postLoadGallery(Request $request){
        $partner_id = $request->partner_id;
        $partner = \App\Partner::find($partner_id);
        foreach ($request->gallery as $gallery_item) {
            if ($gallery_item){
                $picture_extension = $gallery_item->getClientOriginalExtension();
                $image_size = getimagesize($gallery_item);
                $image_width = $image_size[0];
                $image_height = $image_size[1];
                $picture_name = '/assets/img/partners/' . $partner->id .'/gallery' . '/' . $this->generateRandomString() . '.' . $picture_extension;
                Storage::disk('public')->put($picture_name, File::get($gallery_item));
                DB::table('ETKPLUS_PARTNER_PHOTOS')
                    ->insert([
                        'partner_id' => $partner_id,
                        'image_path' => $picture_name,
                        'image_width' => $image_width,
                        'image_height' => $image_height
                        ]);

            } else {
                Session::flash('error', 'Произошла ошибка, файлы загрузить не удалось');
                return redirect()->back();
            }
        }
        Session::flash('success','Изображения загружены. Необходимо дать им названия');
        return redirect()->back();
    }
    /**
     * [postDeleteGalleryItem description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function postDeleteGalleryItem(Request $request){
        $gallery_item_id = $request->gallery_item_id;
        $gallery_item_path = $request->image_path;
        DB::table('ETKPLUS_PARTNER_PHOTOS')
            ->where('id', $gallery_item_id)
            ->delete();
        try {
            Storage::disk('public')->delete($gallery_item_path);   
        } catch (Exception $e) {
            Session::flash('error',$e);
            return redirect()->back();      
        }
        Session::flash('success','Изображение удалено');
        return redirect()->back();
    }

    /**
     * ADDRESSES
     *
     * 
     */
    public function postAddPartnerAddress(Request $request){
        $partner_id = $request->partner_id;
        $name       = $request->name;
        $text       = $request->text;
        $comment    = $request->comment;
        $schedule   = $request->schedule;
        $phones     = $request->phones;
        try {
            DB::table('ETKPLUS_ADDRESSES')
              ->insert([
                'partner_id' => $partner_id,
                'text' => $text,
                'comment' => $comment,
                'schedule' => $schedule,
                'phones' => $phones
                ]);
            Session::flash('success','Адрес успешно добавлен');
            return redirect()->back();

        } catch (Exception $e) {
            Session::flash('error',$e);
            return redirect()->back();              
        }
    }
    public function postDeletePartnerAddress(Request $request){
        $address_id = $request->address_id;
        try {
            DB::table('ETKPLUS_ADDRESSES')
              ->where('id',$address_id)
              ->delete();
            Session::flash('success','Адрес успешно удален');
            return redirect()->back();
        } catch (Exception $e) {
              Session::flash('error',$e);
              return redirect()->back();           
        }
    }
    /**
     * END OF ADDRESSES
     */

}
