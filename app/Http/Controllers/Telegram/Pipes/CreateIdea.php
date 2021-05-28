<?php


namespace App\Http\Controllers\Telegram\Pipes;


use App\Http\Controllers\Telegram\Services\Editor\EditorCreateIdea;
use App\Http\Controllers\Telegram\Services\Editor\EditorWorkWithTask;
use App\Http\Controllers\Telegram\Services\Journalist\JournalistCreateIdea;
use App\Models\Telegram_user;
use App\Models\Temp_idea;
use App\Models\User;
use App\Http\Controllers\Telegram\Traits\PreparationMessage;

class CreateIdea
{
    use PreparationMessage;
    private $message;
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Создаем идею
     * @param $returnData
     * @return mixed
     */
    public function __invoke($returnData) {

        if($returnData['step'] == 'end') {
            return $returnData;
        }
        $telegram_user = Telegram_user::get()->where('telegram_id', $returnData['user']['telegram_id'])->first();

        if(isset($this->message['message']) && $this->message->getMessage()->text == 'Добавить идею') {
            $telegram_user->changeStepsUser('create_idea', null);
        }

        $user = User::get()->where('telegram_id', $returnData['user']['telegram_id'])->first();
        $temp_idea = Temp_idea::get()->where('user_id', $user->id)->first();

        if(!is_null($temp_idea) && $telegram_user->current_step == 'create_idea') {
            switch($telegram_user->role_id) {
                case '4':
                    $completeMessage = (new EditorCreateIdea())->createIdea($telegram_user, $this->message, 'Ваша идея отправлена.');
                    $returnData = $this->preparation($returnData, $completeMessage);
                    break;
                case '5':
                    $completeMessage = (new JournalistCreateIdea())->createIdea($telegram_user, $this->message, 'Ваша идея отправлена редактору и будет им рассмотрена. По результатам вам придет уведомление.');
                    $returnData = $this->preparation($returnData, $completeMessage);
                    break;
            }
        }

        return $returnData;
    }

}
