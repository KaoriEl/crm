<?php

namespace App\Http\Controllers\BotUsers;

use App\Models\Cache;
use App\Models\Comment;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Excel\WorkWithExcel;
use App\Http\Controllers\TelegramBotController as TelegramBot;
use App\Models\Idea;
use App\Models\Post;
use App\Models\Telegram_user;
use App\Models\Temp_idea;
use App\Models\Temp_task;
use Telegram\Bot\Api as Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Commands\Command;
use App\Models\User;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Http\Controllers\BotUsers\EditorController;
use TelegramBot\InlineKeyboardPagination\InlineKeyboardPagination;
use App\Http\Controllers\IdeaController;
use cebe\markdown\GithubMarkdown;

class JournalistController extends Controller
{
    /** @var Api */
    protected $telegram;

    protected $keyboard;

    protected $editor;

    /**
     * Бот контроллер конструктор
     */
    public function __construct()
    {
        $this->telegram = new Api();
        $this->editor = new EditorController();
    }


    /**
     * Получение задач поста
     * @param bool $defaultKeyboard bool $inlineKeyboard, int $user_id, string $callbackData = null
     * @param $inlineKeyboard
     * @param $user_id
     * @param null $callbackData
     * @return void
     * @throws TelegramSDKException
     */
    public function getTasksJournalist($defaultKeyboard, $inlineKeyboard, $user_id, $callbackData = null)
    {
        $user = User::all()->where('telegram_id', $user_id)->first();
        $status = ['status_not_journalist', 'status_not_work', 'status_in_work', 'status_wait_check', 'status_in_completion', 'status_wait_public'];
        $keyboard = Keyboard::make()
            ->inline();
        /* создание кнопок клавиатур
            короткая запись if else
         */
        (Post::all()->where('journalist_id', null)->where('archived_at', null)->where('status_task', 0)->count()) ? $keyboard->row(Keyboard::inlineButton(['text' => 'Без назначения (' . Post::all()->where('journalist_id', 0)->where('archived_at', null)->where('status_task', 0)->count() . ')  ', 'callback_data' => 'status_not_journalist',])
        ) : false;

        (Post::all()->where('journalist_id', $user->id)->where('archived_at', null)->where('status_task', 0)->count()) ? $keyboard->row(Keyboard::inlineButton(['text' => 'Не в работе (' . Post::all()->where('journalist_id', $user->id)->where('archived_at', null)->where('status_task', 0)->count() . ')  ', 'callback_data' => 'status_not_work'])
        ) : false;

        (Post::all()->where('journalist_id', $user->id)->where('archived_at', null)->where('status_task', 1)->where('draft_url', null)->count()) ? $keyboard->row(Keyboard::inlineButton(['text' => 'В работе (' . Post::all()->where('journalist_id', $user->id)->where('archived_at', null)->where('status_task', 1)->where('draft_url', null)->count() . ')', 'callback_data' => 'status_in_work'])
        ) : false;

        (Post::all()->where('journalist_id', $user->id)->where('archived_at', null)->where('draft_url', '!=', null)->where('approved', '==', null)->where('on_moderate', 0)->count()) ? $keyboard->row(Keyboard::inlineButton(['text' => 'Нужна проверка (' . Post::all()->where('journalist_id', $user->id)->where('archived_at', null)->where('draft_url', '!=', null)->where('approved', '===', null)->where('on_moderate', 0)->count() . ')', 'callback_data' => 'status_wait_check'])
        ) : false;

        (Post::all()->where('journalist_id', $user->id)->where('archived_at', null)->where('draft_url', '!=', null)->where('on_moderate', 1)->count()) ? $keyboard->row(Keyboard::inlineButton(['text' => 'На доработке (' . Post::all()->where('journalist_id', $user->id)->where('archived_at', null)->where('draft_url', '!=', null)->where('on_moderate', 1)->count() . ')', 'callback_data' => 'status_in_completion'])
        ) : false;

        (Post::all()->where('journalist_id', $user->id)->where('archived_at', null)->where('approved', 1)->where('publication_url', null)->count()) ? $keyboard->row(Keyboard::inlineButton(['text' => 'Ждет публикации (' . Post::all()->where('journalist_id', $user->id)->where('archived_at', null)->where('approved', 1)->where('publication_url', null)->count() . ')', 'callback_data' => 'status_wait_public'])
        ) : false;

        if ($defaultKeyboard) {
            if ($keyboard->last() == null) {
                $this->telegram->sendMessage([
                    'chat_id' => $user_id,
                    'text' => 'У вас нет задач',
                ]);
            } else {
                $this->telegram->sendMessage([
                    'chat_id' => $user_id,
                    'text' => 'Выберите статус задач, которые необходимо отобразить. ',
                    'reply_markup' => $keyboard,
                ]);
            }

        } else {
            if ($inlineKeyboard) {

                $count = 1;
                switch ($callbackData) {
                    case $status[0]:
                        $posts = Post::all()->where('journalist_id', null)->where('archived_at', null)->where('status_task', 0);
                        $text = "<b>Без назначения: </b> \n";
                        foreach ($posts as $post) {
                            $formattText = $this->formatPostText($post->text);

                            $keyboard = Keyboard::make()
                                ->inline();
                            $keyboard->row(
                                Keyboard::inlineButton(['text' => 'Взять задачу в работу', 'callback_data' => 'take_in_work_' . $post->id]));

                            $deadline_post = $post->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';

                            $text =
                                "Задача № " . $count . ' (' . $post->id . ") \n"
                                . "<b>Название: </b> " . $post->title . " \n"
                                . "<b>Тезисы: </b> "
                                . $formattText . "\n"
                                . "<b>Дедлайн: </b>" . $deadline_post . "\n"
                                . "<b>Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a></b> \n"
                                . " \n";

                            $count++;


                            $this->telegram->sendMessage([
                                'chat_id' => $user_id,
                                'parse_mode' => 'HTML',
                                'text' => $text,
                                'reply_markup' => $keyboard
                            ]);


                        }
                        if (empty($text)) {
                            $text = 'Пусто';
                            $this->telegram->sendMessage([
                                'chat_id' => $user_id,
                                'parse_mode' => 'HTML',
                                'text' => $text,
                            ]);
                        }
                        break;
                    case $status[1]:
                        $text = "<b>Не в работе: </b> \n";
                        $posts = Post::all()->where('journalist_id', $user->id)->where('archived_at', null)->where('status_task', 0);
                        foreach ($posts as $post) {
                            $formattText = $this->formatPostText($post->text);
                            $keyboard = Keyboard::make()
                                ->inline();
                            $keyboard->row(
                                Keyboard::inlineButton(['text' => 'Начать работу', 'callback_data' => 'take_in_work_' . $post->id]));

                            $deadline_post = $post->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';

                            $text = "Задача № " . $count . ' (' . $post->id . ") \n"
                                . "<b>Название: </b> " . $post->title . " \n"
                                . "<b>Тезисы: </b> "
                                . $formattText . "\n"
                                . "Дедлайн: " . $deadline_post . "\n"
                                . "<b>Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a></b> \n"
                                . " \n";

                            $count++;
                            $this->telegram->sendMessage([
                                'chat_id' => $user_id,
                                'parse_mode' => 'HTML',
                                'text' => $text,
                                'reply_markup' => $keyboard
                            ]);
                        }
                        if (empty($text)) {
                            $text = 'Пусто';
                            $this->telegram->sendMessage([
                                'chat_id' => $user_id,
                                'parse_mode' => 'HTML',
                                'text' => $text,
                            ]);
                        }

                        break;
                    case $status[2]:
                        $text = "<b>В работе: </b> \n";
                        $posts = Post::all()->where('journalist_id', $user->id)->where('archived_at', null)->where('status_task', 1)->where('draft_url', null);
                        foreach ($posts as $post) {
                            $keyboard = Keyboard::make()
                                ->inline();
                            $keyboard->row(
                                Keyboard::inlineButton(['text' => 'Отправить ссылку на публикацию', 'callback_data' => 'give_draft_url_' . $post->id]));
                            $formattText = $this->formatPostText($post->text);

                            $deadline_post = $post->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';

                            $text = "Задача № " . $count . ' (' . $post->id . ") \n"
                                . "<b>Название: </b> " . $post->title . " \n"
                                . "<b>Тезисы: </b> "
                                . $formattText . "\n"
                                . "Дедлайн: " . $deadline_post . "\n"
                                . "<b>Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a></b> \n"
                                . " \n";

                            $count++;
                            $this->telegram->sendMessage([
                                'chat_id' => $user_id,
                                'parse_mode' => 'HTML',
                                'text' => $text,
                                'reply_markup' => $keyboard,
                            ]);
                        }
                        if (empty($text)) {
                            $text = 'Пусто';
                            $this->telegram->sendMessage([
                                'chat_id' => $user_id,
                                'parse_mode' => 'HTML',
                                'text' => $text,
                            ]);
                        }

                        break;

                    case $status[3]:
                        $text = "<b>Нужна проверка: </b> \n";
                        $posts = Post::all()->where('journalist_id', $user->id)->where('archived_at', null)->where('draft_url', '!=', null)->where('approved', '===', null)->where('on_moderate', 0);
                        foreach ($posts as $post) {
                            $formattText = $this->formatPostText($post->text);

                            $deadline_post = $post->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';

                            $text .= "Задача № " . $count . ' (' . $post->id . ") \n"
                                . "<b>Название: </b> " . $post->title . " \n"
                                . "<b>Тезисы: </b> "
                                . $formattText . "\n"
                                . "Дедлайн: " . $deadline_post . "\n"
                                . "<b>Ссылка на материал: </b><a href='" . $post->draft_url . "'>" . $post->draft_url . "</a> \n"
                                . "<b>Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a></b> \n"
                                . " \n";

                            $count++;
                        }
                        if (empty($text)) {
                            $text = 'Пусто';
                        }
                        $this->telegram->sendMessage([
                            'chat_id' => $user_id,
                            'parse_mode' => 'HTML',
                            'text' => $text
                        ]);
                        break;
                    case $status[4]:
                        $text = "<b>На доработке: </b> \n";
                        $posts = Post::all()->where('journalist_id', $user->id)->where('archived_at', null)->where('draft_url', '!=', null)->where('on_moderate', 1);
                        foreach ($posts as $post) {
                            $comments = '';
                            foreach ($post->comments as $value) {
                                if ($value->role == 'Журналист') {
                                    $comments .= "<b>Комментарий журналиста: </b>" . $value->text . "\n";
                                } else {
                                    $comments .= "<b>Комментарий редактора: </b>" . $value->text . "\n";
                                }
                            }

                            $deadline_post = $post->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';

                            $comments = $this->formatPostText($comments);
                            $formattText = $this->formatPostText($post->text);
                            $keyboard = Keyboard::make()
                                ->inline();
                            $keyboard->row(
                                Keyboard::inlineButton(['text' => 'Отправить ссылку на публикацию', 'callback_data' => 'give_draft_url_' . $post->id]));

                            $text = "Задача № " . $count . ' (' . $post->id . ") \n"
                                . "<b>Название: </b> " . $post->title . " \n"
                                . "<b>Тезисы: </b> "
                                . $formattText . "\n"
                                . $comments . "\n"
                                . "Дедлайн: " . $deadline_post . "\n"
                                . "<b>Ссылка на материал: </b><a href='" . $post->draft_url . "'>" . $post->draft_url . "</a> \n"
                                . "<b>Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a></b> \n"
                                . " \n";
                            $count++;
                            $this->telegram->sendMessage([
                                'chat_id' => $user_id,
                                'parse_mode' => 'HTML',
                                'text' => $text,
                                'reply_markup' => $keyboard
                            ]);
                        }
                        if (empty($text)) {
                            $text = 'Пусто';
                            $this->telegram->sendMessage([
                                'chat_id' => $user_id,
                                'parse_mode' => 'HTML',
                                'text' => $text,
                            ]);
                        }

                        break;

                    case $status[5]:
                        $text = "<b>Ждет публикации: </b> \n";
                        $posts = Post::all()->where('journalist_id', $user->id)->where('archived_at', null)->where('approved', 1)->where('publication_url', '==', null);
                        foreach ($posts as $post) {
                            $formattText = $this->formatPostText($post->text);
                            $keyboard = Keyboard::make()
                                ->inline();
                            $keyboard->row(
                                Keyboard::inlineButton(['text' => 'Отправить ссылку на публикацию', 'callback_data' => 'give_publication_url_' . $post->id]));
                            $deadline_post = $post->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';

                            $text = "Задача № " . $count . ' (' . $post->id . ") \n"
                                . "<b>Название: </b> " . $post->title . " \n"
                                . "<b>Тезисы: </b> "
                                . $formattText . "\n"
                                . "Дедлайн: " . $deadline_post . "\n"
                                . "<b>Ссылка на материал: </b><a href='" . $post->draft_url . "'>" . $post->draft_url . "</a> \n"
                                . "<b>Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a></b> \n"
                                . " \n";
                            $count++;

                            $this->telegram->sendMessage([
                                'chat_id' => $user_id,
                                'parse_mode' => 'HTML',
                                'text' => $text,
                                'reply_markup' => $keyboard
                            ]);
                        }
                        if (empty($text)) {
                            $text = 'Пусто';
                            $this->telegram->sendMessage([
                                'chat_id' => $user_id,
                                'parse_mode' => 'HTML',
                                'text' => $text,
                            ]);
                        }
                        break;
                    case 'take_tasks':
                        $this->telegram->sendMessage([
                            'chat_id' => $user_id,
                            'text' => 'Выберите статус задач, которые необходимо отобразить. ',
                            'reply_markup' => $keyboard,
                        ]);

                        break;
                }
            }
        }
    }

    /**
     * Диалог журналиста с ботом после того как он взял задачу в роботу.
     * @param string $callback_data
     * @param int $user_id
     * @param string $text
     * @return int
     */
    public function JournalistWorkWithTask($callback_data = null, $user_id, $text = null)
    {
        // значение из таблицы с кэшом
        $cache_user = Cache::all()->where('telegram_id', $user_id)->first();
        $user = User::all()->where('telegram_id', $user_id)->first();
        // список колбеков инлайновых клавиатур
        $callbacks = ['status_', 'take_in_work_', 'take_tasks', 'give_draft_url_', 'yes_comment', 'no_comment', 'give_publication_url_', 'cancel_action'];

        // отмена действия
        if ($callback_data == 'cancel_action' && $cache_user->post_id != null) {
            $cache_user->current_step = $cache_user->forward_step;
            $cache_user->post_id = null;
            $cache_user->save();
            Telegram::sendMessage([
                'chat_id' => $user_id,
                'parse_mode' => 'HTML',
                'text' => 'Действие отменено.',
            ]);
            return 0;


        }

        // если нажата инлайновая кнопка взять в работу/начать работу
        if (strpos($callback_data, 'take_in_work_') !== false) {
            $take_id_post = $callback_data;
            $take_id_post = str_replace($callbacks[1], "", $take_id_post);

            $post = Post::all()->where('id', $take_id_post)->first();

            if ($post->journalist_id != null && $post->journalist_id != $user->id) {
                Telegram::sendMessage([
                    'chat_id' => $user_id,
                    'parse_mode' => 'HTML',
                    'text' => 'У этой задачи уже есть исполнитель',
                ]);
                return 0;
            }

            if ($cache_user->post_id != null) {
                $post = Post::all()->where('id', $cache_user->post_id)->first();
                Telegram::sendMessage([
                    'chat_id' => $user_id,
                    'parse_mode' => 'HTML',
                    'text' => 'Выполняется другая задача: ' . $post->title . "\n" . 'необходимо завершить действия с ней'
                ]);
                return 0;
            }
            //
            $keyboard = Keyboard::make()
                ->inline();
            $keyboard->row(Keyboard::inlineButton(['text' => 'Отправить ссылку на материал', 'callback_data' => 'give_draft_url_' . $post->id]));

            $cache_user->forward_step = null;
            $cache_user->current_step = 'take_in_work';
            $cache_user->save();
            $post->journalist_id = $user->id;
            $post->status_id = 3;
            $post->status_task = true;
            $post->save();
            $text = 'Вы взяли задачу <b>(' . $post->title . ' - ' . $post->id . ') </b> в работу';
            Telegram::sendMessage([
                'chat_id' => $user_id,
                'parse_mode' => 'HTML',
                'text' => $text,
                'reply_markup' => $keyboard,
            ]);

            $this->editor->NotificationEditor($take_id_post, 'journalist_take_task');

            return 0;
        }
        // если нажата кнопка отправить ссылку на материал
        if (strpos($callback_data, 'give_draft_url_') !== false) {
            $take_id_post = $callback_data;
            $take_id_post = str_replace($callbacks[3], "", $take_id_post);
            $post = Post::all()->where('id', $cache_user->post_id)->first();

            if ($cache_user->post_id != null) {
                Telegram::sendMessage([
                    'chat_id' => $user_id,
                    'parse_mode' => 'HTML',
                    'text' => 'Выполняется другая задача: ' . $post->title . "\n" . 'необходимо завершить действия с ней'
                ]);
                return 0;
            }

            $keyboard = Keyboard::make()
                ->inline();
            $keyboard->row(Keyboard::inlineButton(['text' => 'Отменить действие', 'callback_data' => 'cancel_action']));

            $post = Post::all()->where('id', $take_id_post)->first();
            $cache_user->forward_step = 'give_draft_url';
            $cache_user->current_step = 'wait_draft_link';
            $cache_user->post_id = $take_id_post;
            $cache_user->save();
            Telegram::sendMessage([
                'chat_id' => $user_id,
                'parse_mode' => 'HTML',
                'text' => 'Ожидание ссылки на материал для задачи: ' . $post->title,
                'reply_markup' => $keyboard
            ]);

            return 0;
        }
        // если шаг (действие) юзера в данный момент это отправка ссылки на материал, то он читает все сообщения поступающие от юзера
        if ($cache_user->current_step == 'wait_draft_link' && $cache_user->forward_step == 'give_draft_url') {
            $post = Post::all()->where('id', $cache_user->post_id)->first();

            if ($callback_data != null) {
                return 0;
            }

            $keyboard = Keyboard::make()
                ->inline()->row(['text' => 'Да', 'callback_data' => 'yes_comment'], ['text' => 'Нет', 'callback_data' => 'no_comment']);

            $cache_user->forward_step = 'wait_draft_link';
            $cache_user->current_step = 'need_comment';
            $cache_user->save();
            Telegram::sendMessage([
                'chat_id' => $user_id,
                'parse_mode' => 'HTML',
                'text' => 'Нужно добавить комментарий?',
                'reply_markup' => $keyboard,
            ]);

            $post->approved = null;
            $post->on_moderate = 0;
            $post->status_id = 4;
            $post->draft_url = $text;
            $post->save();

            return 0;
        }
        // если нажата инлайновая кнопка да ( при комментировании)
        if ($callback_data == 'yes_comment') {
            $cache_user->forward_step = 'yes_comment';
            $cache_user->current_step = 'wait_comment';
            $cache_user->save();

            $keyboard = Keyboard::make()
                ->inline();
            $keyboard->row(Keyboard::inlineButton(['text' => 'Отменить действие', 'callback_data' => 'cancel_action']));
            Telegram::sendMessage([
                'chat_id' => $user_id,
                'parse_mode' => 'HTML',
                'text' => 'Напишите комментарий к материалу',
                'reply_markup' => $keyboard
            ]);


            return 0;
        }

        // если нажата кнопка нет инлайновая при комментировании
        if ($callback_data == 'no_comment') {
            $this->editor->NotificationEditor($cache_user->post_id, 'journalist_give_draft_url');
            $cache_user->forward_step = 'no_comment';
            $cache_user->current_step = 'send_draft_url';
            $cache_user->post_id = null;
            $cache_user->save();
            Telegram::sendMessage([
                'chat_id' => $user_id,
                'parse_mode' => 'HTML',
                'text' => 'Ссылка на материал отправлена',
            ]);


            return 0;
        }

        // если шаг юзера в данынй момент комментирование при отправке ссылки к материалу, то тело действия прочитывает все сообщение от юзера
        if ($cache_user->current_step == 'wait_comment' && $cache_user->forward_step == 'yes_comment') {
            $post = Post::all()->where('id', $cache_user->post_id)->first();
            $cache_user->forward_step = 'wait_comment';
            $cache_user->current_step = 'send_draft_url';
            $cache_user->post_id = null;
            $cache_user->save();


            $comment = new comment([
                'text' => $text,
                'role' => 'Журналист',
                'post_id' => $post->id,
            ]);
            $comment->save();
            Telegram::sendMessage([
                'chat_id' => $user_id,
                'parse_mode' => 'HTML',
                'text' => 'Комментарий и ссылка на материал успешно отправлены редактору',
            ]);

            $this->editor->NotificationEditor($post->id, 'journalist_give_draft_url');

            return 0;
        }
        // если нажата инлайновая кнопка отправить публикацию на материал
        if (strpos($callback_data, 'give_publication_url_') !== false) {
            $take_id_post = $callback_data;
            $take_id_post = str_replace($callbacks[6], "", $take_id_post);
            $post = Post::all()->where('id', $cache_user->post_id)->first();

            if ($cache_user->post_id != null) {
                Telegram::sendMessage([
                    'chat_id' => $user_id,
                    'parse_mode' => 'HTML',
                    'text' => 'Выполняется другая задача: ' . $post->title . "\n" . 'необходимо завершить действия с ней'
                ]);
                return 0;
            }

            $keyboard = Keyboard::make()
                ->inline();
            $keyboard->row(Keyboard::inlineButton(['text' => 'Отменить действие', 'callback_data' => 'cancel_action']));

            $post = Post::all()->where('id', $take_id_post)->first();
            $cache_user->post_id = $take_id_post;
            $cache_user->forward_step = 'give_publication_url';
            $cache_user->current_step = 'send_publication_url';
            $cache_user->save();
            Telegram::sendMessage([
                'chat_id' => $user_id,
                'parse_mode' => 'HTML',
                'text' => 'Ожидание ссылки на публикацию материала для задачи: ' . $post->title,
                'reply_markup' => $keyboard
            ]);

            return 0;
        }

        // если шаг  юзера в данный момент это отправка сслыки на публикацию материала
        if ($cache_user->current_step == 'send_publication_url' && $cache_user->forward_step == 'give_publication_url') {
            if ($callback_data != null) {
                return 0;
            }
            $post = Post::all()->where('id', $cache_user->post_id)->first();
            $cache_user->forward_step = null;
            $cache_user->current_step = null;
            $cache_user->post_id = null;
            $cache_user->save();
            Telegram::sendMessage([
                'chat_id' => $user_id,
                'parse_mode' => 'HTML',
                'text' => 'Материал опубликован! Задача завершена ',
            ]);
            $post->publication_url = $text;
            $post->status_id = 7;
            $post->save();

            $this->editor->NotificationEditor($post->id, 'journalist_give_publication_url');

            return 0;
        }
    }


    /**
     * Создание идеи через интерфейс телеграмма журналистом
     * @param $user_id
     * @param null $text
     * @param null $callback
     * @return int
     * @throws TelegramSDKException
     */
    public function createIdea($user_id, $text = null, $callback = null)
    {
        $telegram_user = Telegram_user::all()->where('telegram_id', $user_id)->first();
        $user = User::all()->where('telegram_id', $user_id)->first();
        $temp_idea = Temp_idea::all()->where('user_id', $user->id)->first();
        // Создаем массив шагов пользователя в момент работы
        $steps = ['create_idea', 'give_description_idea', 'fire_idea'];

        switch ($telegram_user->current_step) {
            case $steps[0]:

                $telegram_user->forward_step = $telegram_user->current_step;
                $telegram_user->current_step = $steps[1];
                $telegram_user->save();
                $this->telegram->sendMessage([
                    'chat_id' => $user_id,
                    'parse_mode' => 'HTML',
                    'text' => 'Напишите описание идеи',
                ]);

                break;
            case $steps[1]:
                if (empty($text) || is_null($text)) {
                    $this->telegram->sendMessage([
                        'chat_id' => $user_id,
                        'parse_mode' => 'HTML',
                        'text' => 'Введите текстовое описание идеи!',
                    ]);

                    return 0;
                }

                $temp_idea = new Temp_idea();
                $temp_idea->user_id = $user->id;
                $temp_idea->text = (new GithubMarkdown())->parse($text);
                $temp_idea->save();

                $telegram_user->forward_step = $telegram_user->current_step;
                $telegram_user->current_step = $steps[2];
                $telegram_user->save();
                $keyboard = Keyboard::make()
                    ->inline();
                $keyboard->row(Keyboard::inlineButton(['text' => 'Да', 'callback_data' => 'Fidea_y']));
                $keyboard->row(Keyboard::inlineButton(['text' => 'Нет', 'callback_data' => 'Fidea_n']));

                $this->telegram->sendMessage([
                    'chat_id' => $user_id,
                    'parse_mode' => 'HTML',
                    'text' => 'Срочная идея?',
                    'reply_markup' => $keyboard
                ]);

                break;
            case $steps[2]:
                if (empty($callback) || is_null($callback) && $take_tasks = strpos($callback, 'Fidea') !== false) {
                    $this->telegram->sendMessage([
                        'chat_id' => $user_id,
                        'parse_mode' => 'HTML',
                        'text' => 'Ответьте на вопрос про важность идеи',
                    ]);
                    return 0;
                }

                if ($callback == 'Fidea_y') {
                    $temp_idea->read_now = 1;
                } else {
                    $temp_idea->read_now = 0;
                }

                $temp_idea->save();
                $idea = new Idea();
                $idea->fill($temp_idea->toArray());
                $idea->save();
                $temp_idea->delete();

                $telegram_user->forward_step = $telegram_user->current_step;
                $telegram_user->current_step = null;
                $telegram_user->save();

                $this->telegram->sendMessage([
                    'chat_id' => $user_id,
                    'parse_mode' => 'HTML',
                    'text' => 'Ваша идея отправлена редактору и будет им рассмотрена. По результатам вам придет уведомление',
                ]);

                $ideaController = new IdeaController();
                $ideaController->notifyEditors($idea, $user->id);

                break;

        }
        return 'ok';
    }


    private function formatPostText($text)
    {
        $formattText = strip_tags($text, '<b></b><strong></strong><i></i><em></em><a></a><code></code><pre></pre><br /><br>');
        $formattText = str_replace('</p>', ' \n ', $formattText);
        $formattText = str_replace('<p>', '', $formattText);
        $formattText = str_replace('<br />', "\n", $formattText);

        //
        return $formattText;
    }


}
