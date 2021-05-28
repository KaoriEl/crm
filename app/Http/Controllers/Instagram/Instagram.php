<?php

namespace App\Http\Controllers\Instagram;

use App\Http\Controllers\Excel\Parsing;
use App\Models\InstagramAccount;
use App\Models\Setting;
use InstagramAPI\Instagram as Insta;
use Illuminate\Http\Request;
use Instagram\Api;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Instagram\Auth\Checkpoint\ImapClient;
use Instagram\Model\Media;
use GuzzleHttp\Client;

class Instagram
{
    private $instaClient;
    private static $instance;

    public function __construct()
    {
        \InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
        $this->instaClient = new Insta(false, false);
    }


    public function test()
    {
        $this->instaClient = $this->loginAccount(null);

//        dd($this->instaClient);
//        $link = 'https://www.instagram.com/p/CE0gZt6COhr/';
//        $jsonPost = file_get_contents('http://api.instagram.com/oembed?url=' . $link);
//        $jsonPost = json_decode($jsonPost, true);
        $media_id = $this->instaClient->media->getMediaByGraphQl('CG357RFHczX');

        try {
            $id_post = $this->instaClient->media->getInfo($media_id)->getItems()[0]->getPk();
            dd($id_post);
//            $user = $this->instaClient->people->getInfoByName($id_post->getUser()->getUsername());
//            dd($user->getUser()->getFollowerCount());
            $info_by_post = $this->instaClient->business->getMediaInsights($id_post);

//            $info_by_post = $this->instaClient->business->getInsights('1000d');
            dd($info_by_post);
        } catch (\InstagramAPI\Exception\InstagramException $e) {
            dd($e->getMessage());
            $data['error'] = '123';
            return $data;
        }


//        dd($info_by_post);

    }

    /**
     * Логинмся в инстаграмм аккаунте
     * @param null $username
     * @return Insta|int
     */
    public function loginAccount($username)
    {

        $inst_login = '';
        $inst_pass = '';

        $account = InstagramAccount::get()->where('username', $username)->first();

        if (is_null($account)) {
            $inst_login = Setting::where('setting_name', 'inst_login')->first();
            $inst_login = $inst_login->setting_value;

            if (empty($inst_login)) {
                return 0;
            }

            $inst_pass = Setting::all()->where('setting_name', 'inst_pass')->first();
            $inst_pass = $inst_pass->setting_value;
        } else {
            $inst_login = $account->username;
            $inst_pass = $account->password;
        }


        try {
            $this->instaClient->login($inst_login, $inst_pass);
            return $this->instaClient;
        } catch (\Exception $e) {
            return 0;
        }

    }

    public function authorizeNewUser() {
        try {
            $this->instaClient->login($inst_login, $inst_pass);
            return $this->instaClient;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Получаем клиент инстаграма
     * @return Insta
     */

    public function getInstaClient()
    {
        return $this->instaClient;
    }

    /**
     * Возвращение объекта при инициализации
     * @return Instagram
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new Instagram();
        }
        return self::$instance;
    }

    /*
     * Получаем шорткод медиа, для парсинга на медиа ID
     */
    public function getShortCodeInstagram($link)
    {
        $shortcode = str_replace('https://www.instagram.com/p/', '', $link);
        $shortcode = stristr($shortcode, '/', true);

        return $shortcode;
    }


}
