<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Instagram\AuthInstagram;
use App\Http\Controllers\Instagram\Instagram;
use App\Http\Requests\Profile\UpdatePasswordProfileRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Excel\Service\AuthGoogle;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $timezones = cache()->rememberForever('timezones', function () {
            return timezones();
        });

        $googleAPI = new AuthGoogle();
        $setting = Setting::where('setting_name', 'google_auth')->get();
        if ($setting->count() != 0 && $setting[0]['setting_value'] == 'no') {
            $url = $googleAPI->getClient();
        } else {
            $url = '';
        }

        $inst_login = Setting::all()->where('setting_name', 'inst_login')->first();

        $inst_pass = Setting::all()->where('setting_name', 'inst_pass')->first();
        return view('profile.index', compact('timezones', 'setting', 'url', 'inst_login', 'inst_pass'));
    }

    public function password(UpdatePasswordProfileRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();
        if (Hash::check($validated['old_password'], $user->password)) {
            $user->password = Hash::make($validated['password']);
            $user->save();

            return redirect()->route('profile')->with('message', 'Пароль успешно изменен');
        }
        return redirect()->route('profile')->with('error', 'Вы ввели неверный старый пароль');
    }

    public function update(UpdateProfileRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();
        $user->fill($validated);
        $user->save();
        return redirect()->route('profile')->with('message', 'Информация успешно обновлена');
    }

    /**
     * Регистрация аккаунта instagram
     * @param Request $request
     * @return array
     */
    public function authorizeInstagram(Request $request)
    {

        $instagram = new AuthInstagram();

        $username = $request->get('username');
        $password = $request->get('password');
        $sms_code = $request->get('sms_code');
        $api_path = $request->get('api_path');

        if (is_null($sms_code)) {
            $data = $instagram->authorize($username, $password);

            if (!isset($data['error'])) {
                if (Setting::all()->where('setting_name', 'inst_login')->count() == 0) {
                    $setting = new Setting([
                        'setting_name' => 'inst_login',
                        'setting_value' => $username
                    ]);

                    $setting->save();

                    $setting = new Setting([
                        'setting_name' => 'inst_pass',
                        'setting_value' => $password
                    ]);

                    $setting->save();
                } else {
                    $setting = Setting::all()->where('setting_name', 'inst_login')->first();
                    $setting->setting_value = $username;
                    $setting->save();

                    $setting = Setting::all()->where('setting_name', 'inst_pass')->first();
                    $setting->setting_value = $password;
                    $setting->save();
                }
            }

            return $data;
        } else {
            $data = $instagram->authSmsCode($username, $password, $api_path, $sms_code);

            if (!isset($data['error'])) {
                if (Setting::all()->where('setting_name', 'inst_login')->count() == 0) {
                    $setting = new Setting([
                        'setting_name' => 'inst_login',
                        'setting_value' => $username
                    ]);

                    $setting->save();

                    $setting = new Setting([
                        'setting_name' => 'inst_pass',
                        'setting_value' => $password
                    ]);

                    $setting->save();
                } else {
                    $setting = Setting::all()->where('setting_name', 'inst_login')->first();
                    $setting->setting_value = $username;
                    $setting->save();

                    $setting = Setting::all()->where('setting_name', 'inst_pass')->first();
                    $setting->setting_value = $password;
                    $setting->save();
                }
            }


            return $data;
        }
    }
}
