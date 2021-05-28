<?php


namespace App\Http\Controllers\Telegram\Traits;


use App\Http\Controllers\IdeaController;
use App\Models\Idea;
use App\Models\Temp_idea;
use App\Models\User;
use Telegram\Bot\Keyboard\Keyboard;
use cebe\markdown\GithubMarkdown;

trait TraitCreateIdea
{
    /**
     * Трейт для создания идей
     * @param $telegram_user
     * @param $message
     * @param $text_to_user // То что нужно отправить на последнем этапе пользователю
     * @return array
     */
    public function createIdea($telegram_user, $message, $text_to_user) : array {
        $steps = ['create_idea', 'give_description_idea', 'fire_idea'];
        $completeMessage = [];
        $user = User::get()->where('telegram_id', $telegram_user->telegram_id)->first();
        $temp_idea = Temp_idea::get()->where('user_id', $user->id)->first();
        switch($telegram_user->current_step) {
            case $steps[0]:
                $telegram_user->changeStepsUser($steps[1], $telegram_user->current_step);
                $temp_idea = new Temp_idea();
                $temp_idea->text = ' ';
                $temp_idea->user_id = $user->id;
                $temp_idea->save();
                $completeMessage['text'] = 'Напишите описание идеи';
                break;
            case $steps[1]:
                if(isset($message['message']['text']) && empty($message['message']['text']) || is_null($message['message']['text'])) {
                    $completeMessage['text'] = 'Введите текстовое описание идеи!';
                    return $completeMessage;
                }

                $temp_idea->text = (new GithubMarkdown())->parse($message['message']['text']);
                $temp_idea->save();

                $telegram_user->changeStepsUser($steps[2], $telegram_user->current_step);
                $keyboard = Keyboard::make()
                    ->inline();
                $keyboard->row(Keyboard::inlineButton(['text' => 'Да' , 'callback_data' => 'Fidea_y']));
                $keyboard->row(Keyboard::inlineButton(['text' => 'Нет' , 'callback_data' => 'Fidea_n']));

                $completeMessage['text'] = 'Срочная идея?';
                $completeMessage['keyboard'] = $keyboard;
                break;
            case $steps[2]:
                if(!$message->isType('callback_query') || empty(($message->getCallbackQuery()->getData())) ||  strpos($message->getCallbackQuery()->getData(), 'Fidea') === false) {
                    $completeMessage['text'] = 'Ответьте на вопрос про важность идеи!';
                    return $completeMessage;
                }

                $temp_idea = Temp_idea::get()->where('user_id', $user->id)->first();
                if($message->getCallbackQuery()->getData() == 'Fidea_y') {
                    $temp_idea->read_now = 1;
                } else {
                    $temp_idea->read_now = 0;
                }

                $temp_idea->save();


                $idea = new Idea();
                $idea->fill($temp_idea->toArray());
                $idea->save();
                $temp_idea->delete();

                $telegram_user->changeStepsUser(null, $telegram_user->forward_step);

                $completeMessage['text'] = $text_to_user;
                $ideaController = new IdeaController();
                $ideaController->notifyEditors($idea, $user->id);

                break;

        }

        return $completeMessage;
    }
}
