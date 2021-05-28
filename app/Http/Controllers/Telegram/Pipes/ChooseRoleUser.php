<?php


namespace App\Http\Controllers\Telegram\Pipes;


use App\Models\Telegram_user;
use App\Models\Temp_task;
use Telegram\Bot\Keyboard\Keyboard;
use App\Models\User;

class ChooseRoleUser
{

    public function __invoke($message)
    {
        $returnData = [];

        $returnData['user']['telegram_id'] = $message->getMessage()->chat->id;

        $returnData['keyboard']['isset'] = false;
        $telegram_user = Telegram_user::get()->where('telegram_id', $returnData['user']['telegram_id'])->first();
        $user = User::get()->where('telegram_id', $telegram_user->telegram_id)->first();
        $returnData['user']['obj_user'] = $user;
        if(!isset($message['callback_query']) && $message['message']['text'] == 'Выбрать роль') {
            if(Temp_task::get()->where('editor_id', $user->id)->count() > 0) {
                $returnData['text'] = 'Закончите работу с задачей!';
                $returnData['step'] = 'end';
                return $returnData;
            }
            /** TODO: получение всех ролей юзера и использование их
             *
             * $user = User::all()->where('telegram_id',$user_id)->first();
             * $roles_user = $user->load('roles');
             *
             * foreach($roles_user->roles as $role) {
            switch($role->id) {
            case 1:
            $keyboard[][] = array('text' => 'Админ');
            break;
            case 2:
            $keyboard[][] = array('text' => 'Таргет');
            break;
            case 3:
            $keyboard[][] = array('text' => 'Посев');
            break;
            case 4:
            $keyboard[][] = array('text' => 'Главный редактор');
            break;
            case 5:
            $keyboard[][] = array('text' => 'Журналист');
            break;
            case 6:
            $keyboard[][] = array('text' => 'Сммщик');
            break;
            case 7:
            $keyboard[][] = array('text' => 'Комментатор');
            break;
            }
            }
             *
             *
             */

            $user = User::all()->where('telegram_id',$telegram_user->telegram_id)->first();
             $roles_user = $user->load('roles');

            $keyboard = [
                [
                ],
                ];

             foreach ($roles_user->roles as $role) {
                 switch($role->id) {
                     case 4:
                         $keyboard[0][] =  ["text" => "Главный редактор"];
                         break;
                     case 5:
                         $keyboard[0][] = ["text" => "Журналист"];
                         break;
                 }
             }

            $reply_markup = Keyboard::make([
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => false
            ]);

            $telegram_user->changeStepsUser('ask_role', $telegram_user->current_step);
            $returnData['text'] = 'Выберите доступные роли';
            $returnData['keyboard']['isset'] = true;
            $returnData['keyboard']['obj_keyboard'] = $reply_markup;
            $returnData['step'] = 'end';
        } elseif($telegram_user->current_step == 'ask_role' && !isset($message['callback_query'])) {
            $keyboard = [
                [
                    ["text" => "Показать список задач"],
                ],

            ];

            switch($message['message']['text']) {
                case 'Главный редактор':
                    $returnData['text'] = 'Ваша роль: главный редактор';
                    $keyboard[0][] = ['text' => 'Поставить задачу'];
                    $keyboard[0][] = ['text' => 'Добавить идею'];
                    $telegram_user->role_id = '4';
                    $telegram_user->save();
                    break;
                case 'Журналист':
                    $keyboard[0][] = ['text' => 'Добавить идею'];
                    $returnData['text'] = 'Ваша роль: журналист';
                    $telegram_user->role_id = '5';
                    $telegram_user->save();
                    break;
            }


            $reply_markup = Keyboard::make([
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ]);

            $returnData['keyboard']['isset'] = true;
            $returnData['keyboard']['obj_keyboard'] = $reply_markup;

            $returnData['step'] = 'end';
            $telegram_user->changeStepsUser(null, $telegram_user->current_step);
        } else {
            $returnData['step'] = 'next';
        }

        return $returnData;
    }

}
