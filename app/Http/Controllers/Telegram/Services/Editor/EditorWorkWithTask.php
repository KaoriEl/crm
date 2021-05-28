<?php


namespace App\Http\Controllers\Telegram\Services\Editor;

use App\Http\Controllers\Telegram\Services\InterfaceWorkWithTask;
use App\Http\Controllers\Telegram\Traits\Editor\CreateTask;
use App\Http\Controllers\Telegram\Traits\Editor\CheckDraftFromAJournalist;
use App\Models\Telegram_user;
use App\Models\Temp_task;

class EditorWorkWithTask implements InterfaceWorkWithTask
{

    use CreateTask, CheckDraftFromAJournalist;

    /**
     * Обрабатываем запросы редактора и проверяем стадию, которую надо выполнить
     * @param $user
     * @param $message
     * @return array
     */
    public function workWithTask($user, $message) : array {
        $completeMessage = [];
        $telegram_user = Telegram_user::get()->where('telegram_id', $user['telegram_id'])->first();
        $user_id = $user['obj_user']->id;
        $temp_task = Temp_task::get()->where('editor_id',$user_id)->first();

        /**
         * Работаем с созданной вновь задачей
         */
        if(isset($message['message']['text']) && $message['message']['text'] == 'Поставить задачу') {
            $telegram_user->changeStepsUser('create_task', null);
            $completeMessage = $this->create($telegram_user, $user['obj_user'], $message);
        } elseif(!is_null($temp_task)) {
            $completeMessage = $this->create($telegram_user, $user['obj_user'], $message);
        }

        /**
         * Если на доработку/принять публикацию
         */
        if($message->isType('callback_query') && stripos($message->getCallbackQuery()->getData(),'accept_draft_url_') !== false) {
            $id_post = str_replace('accept_draft_url_', '', $message->getCallbackQuery()->getData());
            $completeMessage = $this->updateDraftJournalist($id_post, $telegram_user, false, true);
        } elseif($message->isType('callback_query') && stripos($message->getCallbackQuery()->getData(),'go_to_moderating_') !== false ) {
            $id_post = str_replace('go_to_moderating_', '', $message->getCallbackQuery()->getData());
            $completeMessage = $this->updateDraftJournalist($id_post, $telegram_user, true, false);
        }

        /**
         * Добавляем комментарий редактора к публикации на модерарировании
         */

        if(isset($message['message']['text']) && $telegram_user->current_step == 'wait_moderating_comment') {
            $completeMessage = $this->addCommentToModerate($telegram_user->post_id, $telegram_user, $message['message']['text']);
        }


        return $completeMessage;
    }

}
