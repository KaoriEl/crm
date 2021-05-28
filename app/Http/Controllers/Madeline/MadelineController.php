<?php

namespace App\Http\Controllers\Madeline;

use App\Http\Controllers\Controller;
use App\Models\MadelineUsers;
use Illuminate\Http\Request;

class MadelineController extends Controller
{
    /*
     * Авторизация сессии
     */
    public function authPhone(Request $request) {
        $phone = '7';
        if($request->get('telegram_phone')) {
            $phone .= $request->get('telegram_phone');
            $madeline_user = new MadelineUsers([
                'session_name' => 'redaktor.' . $phone,
                'active' => 0
            ]);
            $madeline_user->save();
        } else {
            $madeline_user = MadelineUsers::all()->last();
        }
        $madeline_users = MadelineUsers::all();

        $madelineProto = new \danog\MadelineProto\API(app_path('Http/Controllers/Madeline/'. $madeline_user->session_name));
        $madelineProto->start();
        $madelineProto->async(false);
        try {
            $user = $madelineProto->getSelf();
        } catch(\Exception $e) {
            $madeline_user->delete();
            $madeline_user->save();
            $this->index();

        }


        if(isset($user['id'])) {
            $madeline_user->active = 1;
            $madeline_user->save();
            $this->index();
        } else {
            $madeline_user->delete();
            $this->index();
        }
    }


    public function index() {
        $madeline_users = MadelineUsers::all();
        return view('profile.telegram', compact('madeline_users'));
    }

    /**
     * Удаляет пользователя из БД.
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id) {
        $user = MadelineUsers::find($id);
        unlink(app_path('Http/Controllers/Madeline/'.$user->session_name));
        unlink(app_path('Http/Controllers/Madeline/'.$user->session_name . '.lock'));
        unlink(app_path('Http/Controllers/Madeline/'.$user->session_name . '.slock'));
        $user->delete();

        $madeline_users = MadelineUsers::all();
        return view('profile.telegram', compact('madeline_users'));
    }

}
