<?php


namespace App\Http\Controllers\Madeline;

use App\Models\MadelineUsers;
use danog\MadelineProto\API;

class ApiMadeline
{
    private $madelineProto;

    public function __construct()
    {
        $this->madelineProto = $this->authorizeInUser();

        if($this->madelineProto !== false) {
            $this->madelineProto->async(false);
        }



    }

    /**
     * Получение клиента для работы с API
     * @return API
     */
    public function getClient() {
        return $this->madelineProto;
    }

    public function get() {
        $this->madelineProto = new \danog\MadelineProto\API(app_path('Http/Controllers/Madeline/redaktor.79994515769'));
        $this->madelineProto->async(false);
        dd($this->madelineProto->getSelf());
    }

    /**
     * Логинимся под пользователя и возвращаем объект маделина
     */
    public function authorizeInUser() {
        $madeline_user = MadelineUsers::get()->where('active', 1)->first();

        if(is_null($madeline_user)) {
            return false;
        }

        try {
            $madeline = new \danog\MadelineProto\API(app_path('Http/Controllers/Madeline/'. $madeline_user->session_name));
        } catch(\Exception $e) {
            $madeline_user->active = 0;
            $madeline_user->save();
            return $this->authorizeInUser();
        }
        return $madeline;
    }


}
