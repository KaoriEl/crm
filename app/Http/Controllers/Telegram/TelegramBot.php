<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Telegram\Pipes\CreateIdea;
use App\Http\Controllers\Telegram\Pipes\GetDetailInfoByPost;
use App\Http\Controllers\Telegram\Pipes\GetListTasks;
use App\Http\Controllers\Telegram\Pipes\WorkWithTask;
use Telegram\Bot\Api;
use League\Pipeline\Pipeline;

use App\Http\Controllers\Telegram\Pipes\ChooseRoleUser;
use Telegram\Bot\Keyboard\Keyboard;

class TelegramBot
{
    private $telegram;
    private static $instance;

    public function __construct()
    {
        $this->telegram = new Api();
    }

    public function getTelegram()
    {
        return $this->telegram;
    }

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new TelegramBot();
        }
        return self::$instance;
    }

    public function webhookHandler()
    {
        $message = $this->telegram->commandsHandler(true);
        // Каждый шаг проверяется для работы пользователем

        // Начало работы с ботом
        if ($message->getMessage()->text == '/start') {
//            $this->sendMessage(['text' => '123', 'user' => ['telegram_id' => $message->getMessage()->chat->id]]);
            $this->telegram->addCommand('App\CommandBot\StartCommand');
            $commandsHandler = $this->telegram->getCommandBus()->handler($message);
            return 'ok';
        }


        $pipeline = (new Pipeline)
            ->pipe(new ChooseRoleUser)
            ->pipe(new GetListTasks($message))
            ->pipe(new GetDetailInfoByPost($message))
            ->pipe(new CreateIdea($message))
            ->pipe(new WorkWithTask($message));

        $returnData = $pipeline->process($message);

        if (!isset($returnData['text'])) {
            $this->sendMessage(['text' => 'Напишите /start для работы с задачами.', 'user' => ['telegram_id' => $message->getMessage()->chat->id]]);
            return 'ok';
        }

        /**
         * Если будут происходит баги с клавиатурой, то данное условие пропустит только текст, а не сломает весь скрипт
         */

        if ($returnData['keyboard']['isset'] == true && !is_string($returnData['keyboard']['obj_keyboard']) && $returnData['keyboard']['obj_keyboard']->count() == 0) {
            $returnData['keyboard']['isset'] = false;
        }


        $this->sendMessage($returnData, $returnData['keyboard']['isset']);

        return 'ok';
    }


    /** Получаем обновление, обарбатываем его
     * @return \Telegram\Bot\Objects\Update[]
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */

    public function getUpdate()
    {
        $updates = $this->telegram->getUpdates(['limit' => 1, 'offset' => '189703787']);
//        dd($updates);


        if (empty($updates)) {
            return 0;
        }

        $message = $updates[0];
        $update_id = $updates[0]['update_id'];

        if ($message->getMessage()->text == '/start') {
//            $this->sendMessage(['text' => '123', 'user' => ['telegram_id' => $message->getMessage()->chat->id]]);
            $this->telegram->addCommand('App\CommandBot\StartCommand');
            $commandsHandler = $this->telegram->getCommandBus()->handler($message);
            return $this->telegram->getUpdates(['offset' => $update_id + 1]);
        }


        // Каждый шаг проверяется для работы пользователем
        $pipeline = (new Pipeline)
            ->pipe(new ChooseRoleUser)
            ->pipe(new GetListTasks($message))
            ->pipe(new GetDetailInfoByPost($message))
            ->pipe(new CreateIdea($message))
            ->pipe(new WorkWithTask($message));

        $returnData = $pipeline->process($message);

        if (!isset($returnData['text'])) {
            $this->sendMessage(['text' => 'Напишите /start для работы с задачами.']);
        }

        $this->sendMessage($returnData, $returnData['keyboard']['isset']);

        return $this->telegram->getUpdates(['offset' => $update_id + 1]);
    }

    /**
     * Отпрвавка сообщений пользователю
     * @param array $returnData
     * @param bool $inlineKeyboard
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function sendMessage(array $returnData, bool $inlineKeyboard = false)
    {
        $options = [];
        if (!$inlineKeyboard) {
            $options = [
                'chat_id' => $returnData['user']['telegram_id'],
                'parse_mode' => 'HTML',
                'text' => $returnData['text'],
            ];
        } else {

            $options = [
                'chat_id' => $returnData['user']['telegram_id'],
                'parse_mode' => 'HTML',
                'text' => $returnData['text'],
                'reply_markup' => $returnData['keyboard']['obj_keyboard']
            ];
        }
        $this->telegram->sendMessage($options);
    }
}
