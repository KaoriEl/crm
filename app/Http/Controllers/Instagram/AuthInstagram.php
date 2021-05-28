<?php


namespace App\Http\Controllers\Instagram;


use App\Models\InstagramAccount;
use App\Models\Setting;

class AuthInstagram
{
    private $instaClient;

    public function __construct()
    {
        $this->instaClient = (Instagram::getInstance())->getInstaClient();
    }

    /**
     * @param $username
     * @param $password
     * Авторизация пользователя
     * @return array
     */

    public function authorize($username, $password) {

        $instagram_account = InstagramAccount::where('username', $username)->count();
        $data = [];

        if($instagram_account) {
            $data['error'] = 'isset_account';
            return $data;
        }

        $user_path = app_path('mgp25/instagram-php/sessions/' . $username);

        $user_tmp_file = $user_path . '/' . $username . '-custom-response.dat';

        # для начала ВАЖНО удалить старые файлы от предыдущих авторизаций
        if (file_exists($user_path . '/' . $username . '-settings.dat')) {
            unlink($user_path . '/' . $username . '-settings.dat');
        }

        if (file_exists($user_path . '/' . $username . '-cookies.dat')) {
            unlink($user_path . '/' . $username . '-cookies.dat');
        }

        try {
            $this->instaClient->login($username, $password);
        }catch(\Exception $e) {
            $data['error'] = $e->getMessage();
            if(strpos($e->getMessage(),'Challenge required.') !== false) {
                $response = $e->getResponse();
//                $this->instaClient->resendChallengeCode($username, $response->getChallenge()->getApiPath(), 1);
                $this->instaClient->sendChallangeCode($response->getChallenge()->getApiPath(),1);
                $data['error'] = 'need_auth';
                $data['api_path'] = $response->getChallenge()->getApiPath();
            }

            if(strpos($e->getMessage(),'The password you entered is incorrect. Please try again.') !== false) {
                $data['error'] = 'bad_pass';
            }

            return $data;
        }

        return $data;
    }

    /**
     * @param $username
     * @param $password
     * @param $api_path
     * @param $sms_code
     * Отправка SMS кода на сервер инстаграма
     * @return array
     */
    public function authSmsCode($username, $password, $api_path, $sms_code)  {
        $data = [];
        try {
            $this->instaClient->finishChallengeLogin($username, $password, $api_path, $sms_code);
        } catch (\Exception $e) {
            $data['error'] = $e;
        }

        return $data;
    }


}
