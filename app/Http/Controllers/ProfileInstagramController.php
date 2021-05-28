<?php


namespace App\Http\Controllers;


use App\Http\Controllers\Excel\Service\AuthGoogle;
use App\Http\Controllers\Instagram\AuthInstagram;
use App\Http\Controllers\Instagram\Instagram;
use App\Models\InstagramAccount;
use App\Models\Setting;
use Illuminate\Http\Request;

class ProfileInstagramController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function index()
    {
        $accounts = InstagramAccount::all();


        return view('profile.instagram', compact('accounts'));
    }

    /**
     * Регистрация аккаунта instagram
     * @param Request $request
     * @return array
     */
    public function authorizeInstagram(Request $request) {

       $instagram = new AuthInstagram();

        $username = $request->get('username');
        $password = $request->get('password');
        $sms_code = $request->get('sms_code');
        $api_path = $request->get('api_path');

        if(is_null($sms_code)) {
            $data = $instagram->authorize($username, $password);
            if(!isset($data['error'])) {
                $client_instagram = Instagram::getInstance();

                $error = false;
                try {
                    $client_instagram->getInstaClient()->login($username, $password);
                } catch (\Exception $e) {
                    $error = true;
                }

                if(!$error) {
                    $name = $client_instagram->getInstaClient()->people->getInfoByName($username)->getUser()->getFullName();
                    $instagram_account = new InstagramAccount([
                        'username' => $username,
                        'password' => $password,
                        'name_account' => $name
                    ]);
                    $instagram_account->save();
                }
            }

            return $data;
        } else {
            $data = $instagram->authSmsCode($username, $password, $api_path, $sms_code);
            if(!isset($data['error'])) {
                $client_instagram = Instagram::getInstance();
                $error = false;
                try {
                    $client_instagram->getInstaClient()->login($username, $password);
                } catch (\Exception $e) {
                    $error = true;
                }

                if(!$error) {
                    $name = $client_instagram->getInstaClient()->people->getInfoByName($username)->getUser()->getFullName();
                    $instagram_account = new InstagramAccount([
                        'username' => $username,
                        'password' => $password,
                        'name_account' => $name
                    ]);
                    $instagram_account->save();
                }
            }
            return $data;
        }
    }

}
