<?php


namespace App\Http\Controllers\Excel\Service;


use App\Models\Setting;
use App\Http\Controllers\ProfileController;

class AuthGoogle
{

    /**
     * Получаем либо ссылку на авторизацию аккаунта для файла, либо же получаем объект клиента для работы с API
     *
     */
    public function getClient()
    {
        $client = new \Google_Client();
        $client->setApplicationName('Get info by task list');
        $client->setScopes(\Google_Service_Sheets::SPREADSHEETS);
        // Файл credentials.json нужно получить в панели управления API google и добавить его в папку public_html
        $client->setAuthConfig(base_path() . '/public_html/credentials.json');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        // Load previously authorized token from a file, if it exists.
        // The file token.json stores the user's access and refresh tokens, and is
        // created automatically when the authorization flow completes for the first
        // time.
        $tokenPath = base_path() . '/public_html/token.json';
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }

        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {
                // Request authorization from the user.
                $client->setRedirectUri($_SERVER['APP_URL'] . '/authCodeGoogle');
                $authUrl = $client->createAuthUrl();
                return $authUrl;
            }
        }

        return $client;
    }

    /**
     * Отправляем код регистрации на сервера Google при авторизации
     *
     */

    public function authCode()
    {
        $client = new \Google_Client();
        $client->setApplicationName('Get info by task list');
        $client->setScopes(\Google_Service_Sheets::SPREADSHEETS_READONLY);
        // Файл credentials.json нужно получить в панели управления API google и добавить его в папку public_html
        $client->setAuthConfig($_SERVER['DOCUMENT_ROOT'] . '/credentials.json');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        $client->setRedirectUri($_SERVER['APP_URL'] . '/authCodeGoogle');

        $code = $this->getParams()['code'];

        // Exchange authorization code for an access token.
        $accessToken = $client->fetchAccessTokenWithAuthCode($code);
        $client->setAccessToken($accessToken);

        // Check to see if there was an error.
        if (array_key_exists('error', $accessToken)) {
            throw new Exception(join(', ', $accessToken));
        }


        // Помещается в public_html
        $tokenPath = 'token.json';

        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }

        $setting = Setting::where('setting_name', 'google_auth')->first();
        $setting->setting_value = 'yes';
        $setting->save();

        file_put_contents($tokenPath, json_encode($client->getAccessToken()));

        $profileController = new ProfileController();
        return $profileController->index();

    }

    private function getParams()
    {
        return $_GET;
    }


}
