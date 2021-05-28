<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;
use App\Models\VKAccounts;
use Illuminate\Http\Request;
use VK\Client\VKApiClient;

class AuthVK extends Controller
{
    /**
     * Разбираю токен регуляркой и сейвлю в бд
     * @param $token
     * @return string
     */
    public function authorizeVK($token)
    {

        $user_id = $this->RegexUserid($token);
        $token = $this->RegexToken($token);
        $this->SaveAcc($token,$user_id);

        return route('profile.vk');

    }

    /**
     * Вычленяю из токена регуляркой юзер айди
     * @param $token
     * @return mixed
     */
    public function RegexUserid($token)
    {
        $findUserid = '/user_id=(\d+)/';
        $str = $token;
        preg_match($findUserid, $str, $user_id, PREG_OFFSET_CAPTURE, 0);
        return $user_id;

    }

    /**
     * Вычленяю из токена токен
     * @param $token
     * @return mixed
     */
    public function RegexToken($token)
    {
        $findToken = '/access_token=(.*?)&/';
        $str = $token;
        preg_match($findToken, $str, $token, PREG_OFFSET_CAPTURE, 0);
        return $token;
    }

    /**
     * Сейв авторизованного аккаунта в бд
     * @param $token
     * @param $user_id
     * @return VKAccounts|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function SaveAcc($token,$user_id)
    {
        if (VKAccounts::where("name", "https://vk.com/id" . $user_id[1][0])->count() > 0){
            $vk_acc = VKAccounts::where("name", "https://vk.com/id" . $user_id[1][0])->first();
            $vk_acc->update([
                'token' => $token[1][0],
                'status' => 1
            ]);

        }else{
            $vk_acc = new VKAccounts([
                'name' => "https://vk.com/id" . $user_id[1][0],
                'token' => $token[1][0],
                'status' => 1
            ]);
            $vk_acc->save();
        }
        return $vk_acc;

    }


}
