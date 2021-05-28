<?php


namespace App\Http\Controllers\Telegram\Pipes;


use App\Http\Controllers\Telegram\Services\Journalist\JournalistWorkWithTask;
use App\Models\Telegram_user;
use Telegram\Bot\Keyboard\Keyboard;
use App\Http\Controllers\Telegram\Services\Editor\EditorWorkWithTask;
use App\Http\Controllers\Telegram\Traits\PreparationMessage;

class WorkWithTask
{
    use PreparationMessage;
    private $message;
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * TODO: Данный pipe является последним и поэтому его всегда следует указывать последним! (Данный блок огромный функционально и поэтому пусть остается последним)
     * @param $returnData
     * @return mixed
     */

    public function __invoke($returnData) {
        if($returnData['step'] == 'end') {
            return $returnData;
        }

        $telegram_user = Telegram_user::get()->where('telegram_id', $returnData['user']['telegram_id'])->first();

        switch($telegram_user->role_id) {
            case '4':
                $completeMessage = (new EditorWorkWithTask())->workWithTask($returnData['user'], $this->message);
                $returnData = $this->preparation($returnData, $completeMessage);
                break;
            case '5':
                $completeMessage = (new JournalistWorkWithTask())->workWithTask($returnData['user'], $this->message);
                $returnData = $this->preparation($returnData, $completeMessage);
                break;
        }

        return $returnData;
    }
}
