<?php

namespace App\Http\Controllers\OK;

use App\Http\Controllers\Controller;
use App\Models\OKAccounts;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class AuthOKController extends Controller
{
    /**
     * Разбираю токен регуляркой и сейвлю в бд
     * @param $token
     * @return string
     */
    public function authorizeOK($token)
    {
        $client = new \GuzzleHttp\Client();
        $code = $this->RegexCode($token);
        $token = $this->GetAccessToken($code,$client);
        $user_id = $this->GetInfoAboutUser($client,$token);
        $this->SaveAcc($token,$user_id);
        dump("Аккаунт авторизован");
        return route('profile.ok');

    }

    /**
     * Вычленяю из токена регуляркой код для получения токена
     * @param $token
     * @return mixed
     */
    public function RegexCode($token)
    {
        $findCode= '/code=(\d+?\w+)/';
        $str = $token;
        preg_match($findCode, $str, $code, PREG_OFFSET_CAPTURE, 0);
        return $code[1][0];

    }

    /**
     * Получаю аксес токен исходя из полученного кода
     * @param $code
     * @param $client
     * @return mixed
     */
    public function GetAccessToken ($code,$client){

        //кидаем запрос для получения токена.
        $url = "https://api.ok.ru/oauth/token.do?code=". $code ."&client_id=512001057121&client_secret=ECBC3062E925E0126232B120&redirect_uri=". config('app.OK_APP_URL') ."&grant_type=authorization_code";

        $request = new Request('POST', $url);
        $response = $client->send($request);
        $jsonResponse = $response->getBody()->getContents();
        $obj = json_decode($jsonResponse);
        try {
            $token = $obj->{'access_token'};
        }catch (\Exception $e){
            dd("Код устарел, попробуйте разрешить приложению доступ еще раз.");
        }

        return $token;


    }

    /**
     * Получаю и инфу о себе для записи ссылкин а юзера в бд
     * Фурмулы для просчета сигнатуры.
     * secret_key = MD5(access_token + application_secret_key)
     * Сортируем и склеиваем параметры запроса и secret_key
     * application_key=PUBLICK_KEYformat=jsonmethod=METHODSECRET_KEY
     * @param $client
     * @param $token
     * @return mixed
     */
    public function GetInfoAboutUser($client,$token){
        //По апи надо шифровать запрос
        $secret_key =  md5($token . "ECBC3062E925E0126232B120");
        $sig  = md5("application_key=CCGCCFKGDIHBABABAformat=jsonmethod=users.getCurrentUser". $secret_key ."");
        $url = "https://api.ok.ru/fb.do?application_key=CCGCCFKGDIHBABABA&format=json&method=users.getCurrentUser&sig=". $sig ."&access_token=". $token ."";

        $request = new Request('POST', $url);
        $response = $client->send($request);
        $jsonResponse = $response->getBody()->getContents();
        $obj = json_decode($jsonResponse);
        $user_id = $obj->{'uid'};

        return $user_id;

    }


    /**
     * Сейв авторизованного аккаунта в бд
     * @param $token
     * @param $user_id
     * @return VKAccounts|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function SaveAcc($token,$user_id)
    {

        if (OKAccounts::where("name", "https://ok.ru/profile/" . $user_id)->count() > 0){
            $ok_acc = OKAccounts::where("name", "https://ok.ru/profile/" . $user_id)->first();
            $ok_acc->update([
                'token' => $token,
                'status' => 1
            ]);

        }else{
            $ok_acc = new OKAccounts([
                'name' => "https://ok.ru/profile/" . $user_id,
                'token' => $token,
                'status' => 1
            ]);
            $ok_acc->save();
        }
        return $ok_acc;

    }
}
