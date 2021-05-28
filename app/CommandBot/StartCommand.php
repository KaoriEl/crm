<?php

namespace App\CommandBot;

use Illuminate\Support\Facades\Auth;
use Telegram\Bot\Commands\Command;
use App\Models\User;
use App\Models\Telegram_user;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Keyboard\Keyboard;
use Spatie\Permission\Models\Role;


/**
 * Class StartCommand
 */
class StartCommand extends Command
{
    /**
     * @var string Название команды
     */
    protected $name = 'start';

    /**
     * @var array Алиасы
     */

    /**
     * @var string Описание команды
     */
    protected $description = 'Начать работу с ботом';

    /**
     * Детект нажатие команды /start
     * Возвращает ответ пользователю
     * @return void
     * @throws TelegramSDKException
     */
    public function handle()
    {
        $update = $this->getUpdate();

        $user_id = $update->getMessage()->from->id;

        $username = $update->getMessage()->chat->username;
        $name = $update->getMessage()->from->first_name;
        $text_chat = $update->getMessage()->text;

        $telegram_user = Telegram_user::where('telegram_id', $user_id)->first();
        $user = User::where('phone', $username)->first();

        if (!is_null($user) && is_null($telegram_user)) {
            $text = "Происходит процесс авторизации. Напишите /start еще раз.";
            $new_user_cache = new Telegram_user([
                'username' => $username,
                'telegram_id' => $user_id,
                'current_step' => "start",
            ]);
            $new_user_cache->save();
            $user->telegram_id = $user_id;
            $user->save();
            $this->replyWithMessage(['text' => $text]);

            return 'Ok';
        } elseif (!is_null($telegram_user) && !is_null($user)) {

            $text = "Добро пожаловать, " . $name . " :)";
            $user = User::where('phone', $username)->first();
            $user->telegram_id = $user_id;
            $user->save();

            $user = User::where('telegram_id', $user_id)->first();

            $telegram_user = Telegram_user::where('telegram_id', $user_id)->first();
            $telegram_user->current_step = null;
            $telegram_user->role_id = null;
            $telegram_user->post_id = null;
            $telegram_user->forward_step = null;
            $telegram_user->save();

            $roles_user = $user->load('roles');


            if ($roles_user->roles->count() > 1) {
                $keyboard = [
                    [
                        ["text" => "Выбрать роль"],


                    ],

                ];
                $reply_markup = Keyboard::make([
                    'keyboard' => $keyboard,
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true
                ]);


                $this->telegram->sendMessage([
                    'chat_id' => $user_id,
                    'text' => 'Необходима авторизация под ролью',
                    'reply_markup' => $reply_markup
                ]);


                return 'Ok';
            } else {

                switch ($roles_user->roles[0]->id) {
                    case 5:
                        $keyboard = [
                            [
                                ["text" => "Показать список задач"],
                                ["text" => "Добавить идею"],
                            ],

                        ];
                        $reply_markup = Keyboard::make([
                            'keyboard' => $keyboard,
                            'resize_keyboard' => true,
                            'one_time_keyboard' => false
                        ]);
                        $this->telegram->sendMessage([
                            'chat_id' => $user_id,
                            'text' => 'Добро пожаловать! Выберите действие.',
                            'reply_markup' => $reply_markup
                        ]);
                        $telegram_user->role_id = $roles_user->roles[0]->id;
                        $telegram_user->save();
                        return 'Ok';
                        break;
                    case 4:
                        //
                        $keyboard = [
                            [
                                ["text" => "Показать список задач"],
                                ["text" => "Поставить задачу"],
                            ],

                        ];
                        $reply_markup = Keyboard::make([
                            'keyboard' => $keyboard,
                            'resize_keyboard' => true,
                            'one_time_keyboard' => false
                        ]);
                        $this->telegram->sendMessage([
                            'chat_id' => $user_id,
                            'text' => 'Добро пожаловать! Выберите действие.',
                            'reply_markup' => $reply_markup
                        ]);
                        $telegram_user->role_id = $roles_user->roles[0]->id;
                        $telegram_user->save();
                        return 'Ok';
                        break;
                }
            }

            $this->telegram->sendMessage([
                'chat_id' => $user_id,
                'text' => 'Добро пожаловать!'
            ]);

        } elseif (is_null($user)) {
            $text = "Вы не авторизованы как пользователь. Обратитесь к администратору.";
            $this->replyWithMessage(['text' => $text]);
            return 'Ok';
        }

        return 'Ok';
    }
}
