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
 * Class PostCommand
 */
class PostCommand extends Command
{
    /**
     * @var string Название команды
     */
    protected $name = 'post';


    /**
     * @var string Описание команды
     */
    protected $description = 'Получить информацию бота';

    /**
     * Детект нажатие команды /post
     * Возвращает ответ пользователю
     * @return string
     * @throws TelegramSDKException
     */
    public function handle()
    {
        $update = $this->getUpdate();

        $user_id = $update->getMessage()->from->id;

        $username = $update->getMessage()->from->username;
        $name = $update->getMessage()->from->first_name;
        $text_chat = $update->getMessage()->text;

        $this->telegram->sendMessage([
            'chat_id' => $user_id,
            'text' => 'xddd'
            ]);

        return 'Ok';
    }
}
