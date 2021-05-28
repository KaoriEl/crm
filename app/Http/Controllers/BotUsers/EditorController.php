<?php

namespace App\Http\Controllers\BotUsers;

use App\Enums\PostTargetStatusesEnum;
use App\Models\Cache;
use App\Models\Comment;
use App\Http\Controllers\Controller;
use App\Http\Controllers\IdeaController;
use App\Http\Controllers\TelegramBotController;
use App\Models\Idea;
use App\Models\Post;
use App\Models\Project;
use App\Models\SmmLink;
use App\Models\ModelsSeedLinks;
use App\Models\SocialNetwork;
use App\Models\Telegram_user;
use App\Models\Temp_idea;
use App\Models\Temp_task;
use App\Models\User;
use Telegram\Bot\Api as Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Commands\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Http\Controllers\TelegramBotController as TelegramBot;
use Telegram\Bot\Objects\MessageEntity;
use TelegramBot\InlineKeyboardPagination\InlineKeyboardPagination;
use cebe\markdown\GithubMarkdown;

class EditorController extends Controller
{
    /** @var Api */
    protected $telegram;

    /**
     * Бот контроллер конструктор
     */
    public function __construct()
    {
        $this->telegram = new Api();

    }


    /**
     * Создание новой задачи редактором
     * @param $user_id
     * @param null $callback_data , int $user_id
     * @param null $text
     * @return int
     * @throws TelegramSDKException
     */
    public function EditorCreateTask($user_id, $callback_data = null, $text = null)
    {
        $telegramBot = new TelegramBot($this->telegram);
        // кэш_юзер - хранит шаги юзеров и пользователей телеграмма
        $cache_user = Cache::all()->where('telegram_id', $user_id)->first();
        $user = User::all()->where('telegram_id', $user_id)->first();

        // Создаем массив шагов пользователя в момент работы
        $steps = ['create_task', 'add_description', 'give_description', 'give_projects', 'give_journalist', 'give_deadline',
            'need_posting', 'wait_posting', 'no_posting', 'need_targeting', 'wait_targeting', 'no_targeting', 'need_seeding',
            'wait_seeding', 'no_seeding', 'need_comment', 'wait_comment'];
        // Определяем действия выбора инлайновых клавиатур, end_create - окончание создание задачи
        $actions = ['yes', 'no', 'end_create'];

        // наш объект задачи основной, в него вносится вся информация промежуточная о задаче
        $temp_task = Temp_task::all()->where('telegram_id', $user_id)->first();

        // В зависимости от шага пользователя выполняется определенный блок с кодом
        switch ($cache_user->current_step) {
            case $steps[0]:
                $cache_user->forward_step = $steps[0];
                $cache_user->current_step = $steps[1];
                $cache_user->save();
                $this->telegram->sendMessage([
                    'chat_id' => $user_id,
                    'text' => 'Напишите название задачи на 1 строке и тезисы на 2 строке',
                ]);
                return 0;
                break;
            case $steps[1]:
                $text = trim($text);
                $regular = "/[\n,]+/";
                $message = preg_split($regular, $text);
                $header = $message[0];
                unset($message[0]);
                $text = implode("\n", $message);

                if (empty($text)) {
                    $this->telegram->sendMessage([
                        'chat_id' => $user_id,
                        'text' => 'Напишите тезисы на второй строке!',
                    ]);
                    return 0;
                }

                $temp_task = new Temp_task([
                    'title' => $header,
                    'text' => $text,
                    'telegram_id' => $user_id,
                    'editor_id' => $user->id,
                ]);
                $temp_task->save();

                $cache_user->forward_step = $steps[1];
                $cache_user->current_step = $steps[3];
                $cache_user->save();
                $keyboard = Keyboard::make()
                    ->inline();
                foreach ($user->projects as $project) {
                    $keyboard->row(
                        Keyboard::inlineButton(['text' => $project->name, 'callback_data' => 'project_' . $project->id]));
                }

                $this->telegram->sendMessage([
                    'chat_id' => $user_id,
                    'text' => 'Выберите проект:',
                    'reply_markup' => $keyboard
                ]);
                return 0;
                break;
            case $steps[2]:
                // Заказчику необходимо, чтобы при первом шаге заполнялся тезис и тайтл задачи
//                $cache_user->forward_step = $steps[2];
//                $cache_user->current_step = $steps[3];
//                $temp_task->text = $text;
//                $temp_task->save();
//                $cache_user->save();
//
//                $keyboard = Keyboard::make()
//                    ->inline();
//                foreach($user->projects as $project) {
//                    $keyboard->row(
//                        Keyboard::inlineButton(['text' => $project->name , 'callback_data' => 'project_'.$project->id]));
//                }
//
//                $this->telegram->sendMessage([
//                    'chat_id' => $user_id,
//                    'text' => 'Выберите проект',
//                   'reply_markup' => $keyboard
//                ]);
                return 0;
                break;
            case $steps[3]:
                // проверка чтобы текст не писался во времена инлайновых кнопок
                if (!empty($text))
                    return 0;

//
                $cache_user->current_step = $steps[4];
                $cache_user->forward_step = $steps[3];
                $cache_user->save();


                $take_id_project = $callback_data;
                $take_id_project = str_replace('project_', "", $take_id_project);

                $temp_task->project_id = $take_id_project;
                $temp_task->save();
                $project = Project::all()->where('id', $take_id_project)->first();
                $keyboard = Keyboard::make()
                    ->inline();

                foreach ($project->users as $user) {
                    $keyboard->row(
                        Keyboard::inlineButton(['text' => $user->name, 'callback_data' => 'journalist_' . $user->id]));
                }

                $keyboard->row(
                    Keyboard::inlineButton(['text' => 'Без назначения', 'callback_data' => 'journalist_null']));

                $this->telegram->sendMessage([
                    'chat_id' => $user_id,
                    'text' => 'Выберите журналиста:',
                    'reply_markup' => $keyboard
                ]);
                return 0;
                break;
            case $steps[4]:
                if (!empty($text))
                    return 0;

                $cache_user->forward_step = $steps[4];
                $cache_user->current_step = $steps[5];
                $cache_user->save();

                $take_id_jorunalist = $callback_data;
                $take_id_jorunalist = str_replace('journalist_', "", $take_id_jorunalist);

                if ($take_id_jorunalist != "null") {
                    $temp_task->journalist_id = $take_id_jorunalist;
                    $temp_task->save();
                }

                $this->telegram->sendMessage([
                    'chat_id' => $user_id,
                    'text' => 'Чтобы задать срок задачи, отправьте в ответном сообщении количество часов до дедлайна',
                ]);

                return 0;
                break;
            case $steps[5]:
                if (!ctype_digit($text) || !empty($callback_data)) {
                    $this->telegram->sendMessage([
                        'chat_id' => $user_id,
                        'text' => 'Введите числовое значение времени!',
                    ]);
                    return 0;
                }
                $cache_user->forward_step = $steps[5];
                $cache_user->current_step = $steps[6];
                $cache_user->save();
                $current_time = $temp_task->created_at;
                $current_time = strtotime($current_time);
                $current_time = strtotime("+" . $text . " hours");
                $temp_task->expires_at = $current_time;
                $temp_task->save();

                $keyboard = Keyboard::make()
                    ->inline()
                    ->row(Keyboard::inlineButton(['text' => 'Да', 'callback_data' => 'yes']))
                    ->row(Keyboard::inlineButton(['text' => 'Нет', 'callback_data' => 'no']))
                    ->row(Keyboard::inlineButton(['text' => 'Завершить постановку задачи', 'callback_data' => 'end_create']));
                $this->telegram->sendMessage([
                    'chat_id' => $user_id,
                    'text' => 'Размещаем в соцсетях?',
                    'reply_markup' => $keyboard,
                ]);
                return 0;
                break;
            case $steps[6]:
                if (!empty($text))
                    return 0;

                $cache_user->forward_step = $steps[6];
                $cache_user->save();

                $take_action = $callback_data;

                foreach ($actions as $key => $value) {
                    $status_action = strpos($callback_data, $value);
                    if ($status_action !== false) {
                        $take_action = $value;
                    }
                }

                switch ($take_action) {
                    case $actions[0]:
                        $cache_user->current_step = $steps[7];
                        $cache_user->save();
                        $text = "Введите цифрами социальные сети: \n"
                            . "1 - Вконтакте \n"
                            . "2 - Одноклассники \n"
                            . "3 - Facebook \n"
                            . "4 - Instagram \n"
                            . "5 - Я.Дзен \n"
                            . "6 - Я.Район \n"
                            . "7 - Youtube \n"
                            . "8 - Telegram \n"
                            . "0 - Выбрать соц сети, которые уже привязаны к проекту \n"
                            . "Пример: 123 Разместить в данных соц-сетях \n"
                            . "Напишите комментарий на второй строке \n";
                        $this->telegram->sendMessage([
                            'chat_id' => $user_id,
                            'text' => $text,
                        ]);
                        break;
                    case $actions[1]:
                        $cache_user->current_step = $steps[9];
                        $cache_user->save();
                        $text = "Делаем таргетированную рекламу?";
                        $keyboard = Keyboard::make()
                            ->inline()
                            ->row(Keyboard::inlineButton(['text' => 'Да', 'callback_data' => 'yes']))
                            ->row(Keyboard::inlineButton(['text' => 'Нет', 'callback_data' => 'no']))
                            ->row(Keyboard::inlineButton(['text' => 'Завершить постановку задачи', 'callback_data' => 'end_create']));

                        $this->telegram->sendMessage([
                            'chat_id' => $user_id,
                            'text' => $text,
                            'reply_markup' => $keyboard,
                        ]);

                        break;
                    case $actions[2]:
                        $post = new Post();
                        $post->fill($temp_task->toArray());

                        if ($post->journalist_id != null) {
                            $post->status_id = 1;
                        } else {
                            $post->status_id = 2;
                        }

                        $post->save();
                        $cache_user->forward_step = $cache_user->current_step;
                        $cache_user->current_step = null;
                        $cache_user->post_id = null;
                        $cache_user->save();
                        $info_new_task = '';
                        $deadline_post = $post->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';
                        $formattText = $this->formatPostText($temp_task->text);
                        (!is_null($temp_task->journalist_id)) ? $user_journalist = User::find($temp_task->journalist_id)->name : $user_journalist = 'Без назначения';
                        $info_new_task .= 'Задача <b>' . $temp_task->title . '</b> создана!' . "\n"
                            . '<b>Тезисы:  </b>' . "\n"
                            . $formattText . "\n"
                            . "Дедлайн: " . $deadline_post . "\n"
                            . '<b>Исполнитель: </b>' . $user_journalist . "\n";

                        if ($temp_task->posting) {
                            $info_new_task .= '<b>Соц-сети:</b> ';
                            ($temp_task->posting_to_vk) ? $info_new_task .= 'ВК, ' : '';
                            ($temp_task->posting_to_ok) ? $info_new_task .= 'OK, ' : '';
                            ($temp_task->posting_to_fb) ? $info_new_task .= 'FB, ' : '';
                            ($temp_task->posting_to_ig) ? $info_new_task .= 'IG, ' : '';
                            ($temp_task->posting_to_y_dzen) ? $info_new_task .= 'Я.Дзен, ' : '';
                            ($temp_task->posting_to_y_street) ? $info_new_task .= 'Я.Районы, ' : '';
                            ($temp_task->posting_to_yt) ? $info_new_task .= 'YT, ' : '';
                            ($temp_task->posting_to_tt) ? $info_new_task .= 'TT, ' : '';
                            ($temp_task->posting_to_tg) ? $info_new_task .= 'TG, ' : '';

                            $info_new_task = Str::replaceLast(', ', '', $info_new_task);
                            $info_new_task .= "\n";


                            if ($temp_task->targeting) {
                                $info_new_task .= "<b>Тарегтированная реклама:</b> Да \n";

                            }

                            if ($temp_task->seeding) {
                                $info_new_task .= "<b>Посевы:</b> Да \n";
                            }

                            if ($temp_task->commenting) {
                                $info_new_task .= "<b>Комментирование:</b> Да  \n";

                            }

                        }

                        $info_new_task .= "<b>Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a></b> \n";

                        $temp_task->delete();
                        $this->telegram->sendMessage([
                            'chat_id' => $user_id,
                            'parse_mode' => 'HTML',
                            'text' => $info_new_task,
                        ]);
                        $telegramBot->storeMessage($post);
                        break;
                }


                return 0;
                break;
            case $steps[7]:
                if (!empty($callback_data)) {
                    $this->telegram->sendMessage([
                        'chat_id' => $user_id,
                        'text' => 'Выберите соц-сети!',
                    ]);
                    return 0;
                }

                $temp_task->posting = 1;

                $text = trim($text);
                $regular = "/[\s,]+/";
                $message = preg_split($regular, $text);
                $take_social_posting = $message[0];
                unset($message[0]);
                $comment = implode(" ", $message);


                $text_to_user = "Вы выбрали соц-сети: ";
                for ($i = 0; $i < Str::length($take_social_posting); $i++) {
                    switch ($take_social_posting[$i]) {
                        case 0:
                            $count = 0;
                            $project = Project::all()->where('id', $temp_task->project_id)->first();
                            if ($project->vk) {
                                $temp_task->posting_to_vk = 1;
                                $text_to_user .= 'вконтакте,';
                                $count++;
                            }
                            if ($project->ok) {
                                $temp_task->posting_to_ok = 1;
                                $text_to_user .= 'одноклассники,';
                                $count++;
                            }
                            if ($project->fb) {
                                $temp_task->posting_to_fb = 1;
                                $text_to_user .= 'facebook,';
                                $count++;
                            }
                            if ($project->insta) {
                                $temp_task->posting_to_ig = 1;
                                $text_to_user .= 'instagram,';
                                $count++;
                            }
                            if ($project->y_dzen) {
                                $temp_task->posting_to_y_dzen = 1;
                                $text_to_user .= 'я.дзен,';
                                $count++;
                            }
                            if ($project->y_street) {
                                $temp_task->posting_to_y_street = 1;
                                $text_to_user .= 'я.улица,';
                                $count++;
                            }
                            if ($project->yt) {
                                $temp_task->posting_to_yt = 1;
                                $text_to_user .= 'youtube,';
                                $count++;
                            }

                            if ($project->tt) {
                                $temp_task->posting_to_tt = 1;
                                $text_to_user .= 'tikTok,';
                                $count++;
                            }

                            if ($project->tg) {
                                $temp_task->posting_to_tg = 1;
                                $text_to_user .= 'telegram,';
                                $count++;
                            }

                            if ($count == 0) {
                                $this->telegram->sendMessage([
                                    'chat_id' => $user_id,
                                    'parse_mode' => 'HTML',
                                    'text' => 'В проекте нет привязанных соц сетей, поэтому выберите из списка.',
                                ]);
                                return 0;
                            }
                            $text_to_user = Str::replaceLast(',', '', $text_to_user);
                            $temp_task->save();
                            break;
                        case 1:
                            $temp_task->posting_to_vk = 1;
                            $text_to_user .= "вконтакте, ";
                            break;
                        case 2:
                            $temp_task->posting_to_ok = 1;
                            $text_to_user .= "одноклассники, ";
                            break;
                        case 3:
                            $temp_task->posting_to_fb = 1;
                            $text_to_user .= "facebook, ";
                            break;
                        case 4:
                            $temp_task->posting_to_ig = 1;
                            $text_to_user .= "instagram, ";
                            break;
                        case 5:
                            $temp_task->posting_to_y_dzen = 1;
                            $text_to_user .= "я.дзен, ";
                            break;
                        case 6:
                            $temp_task->posting_to_y_street = 1;
                            $text_to_user .= "я.район, ";
                            break;
                        case 7:
                            $temp_task->posting_to_yt = 1;
                            $text_to_user .= "youtube, ";
                            break;
                        case 8:
                            $temp_task->posting_to_tg = 1;
                            $text_to_user .= "telegram, ";
                            break;
                        case 9:
                            $temp_task->posting_to_tt = 1;
                            $text_to_user .= "tikTok, ";
                            break;
                        default:
                            $text_to_user = "Ввели некорректные данные!";
                            $temp_task->posting = 0;
                            $temp_task->save();
                            $this->telegram->sendMessage([
                                'chat_id' => $user_id,
                                'text' => $text_to_user,
                            ]);
                            return 0;
                            break;
                    }
                }

                $cache_user->forward_step = $steps[7];
                $cache_user->current_step = $steps[9];
                $cache_user->save();
                $temp_task->posting_text = $comment;
                $temp_task->save();
                $text_to_user = Str::replaceLast(',', '', $text_to_user);
                $text_to_user .= "\n"
                    . "<b>Делаем таргетированную рекламу?</b>";

                $keyboard = Keyboard::make()
                    ->inline()
                    ->row(Keyboard::inlineButton(['text' => 'Да', 'callback_data' => 'yes']))
                    ->row(Keyboard::inlineButton(['text' => 'Нет', 'callback_data' => 'no']))
                    ->row(Keyboard::inlineButton(['text' => 'Завершить постановку задачи', 'callback_data' => 'end_create']));
                $this->telegram->sendMessage([
                    'chat_id' => $user_id,
                    'parse_mode' => 'HTML',
                    'text' => $text_to_user,
                    'reply_markup' => $keyboard,

                ]);

                return 0;
                break;
            case $steps[8]:
                // )))))))))))))))))))))))))))
                return 0;
                break;
            case $steps[9]:
                if (!empty($text))
                    return 0;

                $cache_user->forward_step = $steps[9];
                $cache_user->save();

                $take_action = $callback_data;

                foreach ($actions as $key => $value) {
                    $status_action = strpos($callback_data, $value);
                    if ($status_action !== false) {
                        $take_action = $value;
                    }
                }

                switch ($take_action) {
                    case $actions[0]:
                        $cache_user->current_step = $steps[10];
                        $cache_user->save();
                        $text = "Введите цифрами социальные сети: \n"
                            . "1 - Вконтакте \n"
                            . "2 - Одноклассники \n"
                            . "3 - Facebook \n"
                            . "4 - Instagram \n"
                            . "5 - Я.Дзен \n"
                            . "6 - Я.Район \n"
                            . "7 - Youtube \n"
                            . "8 - Telegram \n"
                            . "0 - Выбрать соц сети, которые уже привязаны к проекту \n"
                            . "Пример: 123 Разместить в данных соц-сетях \n"
                            . "Напишите комментарий на второй строке \n";
                        $this->telegram->sendMessage([
                            'chat_id' => $user_id,
                            'text' => $text,
                        ]);
                        //
                        break;
                    case $actions[1]:
                        $cache_user->current_step = $steps[12];
                        $cache_user->save();
                        $text = "Нужен посев?";
                        $keyboard = Keyboard::make()
                            ->inline()
                            ->row(Keyboard::inlineButton(['text' => 'Да', 'callback_data' => 'yes']))
                            ->row(Keyboard::inlineButton(['text' => 'Нет', 'callback_data' => 'no']))
                            ->row(Keyboard::inlineButton(['text' => 'Завершить постановку задачи', 'callback_data' => 'end_create']));

                        $this->telegram->sendMessage([
                            'chat_id' => $user_id,
                            'text' => $text,
                            'reply_markup' => $keyboard,
                        ]);
//
                        break;
                    case $actions[2]:
                        $post = new Post();
                        $post->fill($temp_task->toArray());
                        if ($post->journalist_id != null) {
                            $post->status_id = 1;
                        } else {
                            $post->status_id = 2;
                        }

                        $post->save();
                        $cache_user->forward_step = $cache_user->current_step;
                        $cache_user->current_step = null;
                        $cache_user->post_id = null;
                        $cache_user->save();
                        $info_new_task = '';
                        $deadline_post = $post->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';
                        $formattText = $this->formatPostText($temp_task->text);
                        (!is_null($temp_task->journalist_id)) ? $user_journalist = User::find($temp_task->journalist_id)->name : $user_journalist = 'Без назначения';
                        $info_new_task .= 'Задача <b>' . $temp_task->title . '</b> создана!' . "\n"
                            . '<b>Тезисы:  </b>' . "\n"
                            . $formattText . "\n"
                            . "Дедлайн: " . $deadline_post . "\n"
                            . '<b>Исполнитель: </b>' . $user_journalist . "\n";

                        if ($temp_task->posting) {
                            $info_new_task .= '<b>Соц-сети:</b> ';
                            ($temp_task->posting_to_vk) ? $info_new_task .= 'ВК, ' : '';
                            ($temp_task->posting_to_ok) ? $info_new_task .= 'OK, ' : '';
                            ($temp_task->posting_to_fb) ? $info_new_task .= 'FB, ' : '';
                            ($temp_task->posting_to_ig) ? $info_new_task .= 'IG, ' : '';
                            ($temp_task->posting_to_y_dzen) ? $info_new_task .= 'Я.Дзен, ' : '';
                            ($temp_task->posting_to_y_street) ? $info_new_task .= 'Я.Районы, ' : '';
                            ($temp_task->posting_to_yt) ? $info_new_task .= 'YT, ' : '';
                            ($temp_task->posting_to_tt) ? $info_new_task .= 'TT, ' : '';
                            ($temp_task->posting_to_tg) ? $info_new_task .= 'TG, ' : '';

                            $info_new_task = Str::replaceLast(', ', '', $info_new_task);
                            $info_new_task .= "\n";


                            if ($temp_task->targeting) {
                                $info_new_task .= "<b>Тарегтированная реклама:</b> Да \n";

                            }

                            if ($temp_task->seeding) {
                                $info_new_task .= "<b>Посевы:</b> Да \n";
                            }

                            if ($temp_task->commenting) {
                                $info_new_task .= "<b>Комментирование:</b> Да  \n";

                            }

                        }

                        $info_new_task .= "<b>Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a></b> \n";


                        $temp_task->delete();
                        $this->telegram->sendMessage([
                            'chat_id' => $user_id,
                            'parse_mode' => 'HTML',
                            'text' => $info_new_task,
                        ]);
                        $telegramBot->storeMessage($post);
                        break;
                }
                return 0;
                break;
            case $steps[10]:
                if (!empty($callback_data)) {
                    $this->telegram->sendMessage([
                        'chat_id' => $user_id,
                        'text' => 'Выберите соц-сети!',
                    ]);
                    return 0;
                }

                $temp_task->targeting = 1;
                /*
                $text = trim($text);
                $array = explode(" ", $text);
                $take_social_target = $array[0];
                unset($array[0]);
                $comment = implode(" ",$array);
                    */

                $regular = "/[\s,]+/";
                $text = trim($text);
                $message = preg_split($regular, $text);
                $take_social_target = $message[0];
                unset($message[0]);
                $comment = implode(" ", $message);

                $text_to_user = "Вы выбрали соц-сети: ";
                for ($i = 0; $i < Str::length($take_social_target); $i++) {
                    switch ($take_social_target[$i]) {
                        case 0:
                            $count = 0;
                            $project = Project::all()->where('id', $temp_task->project_id)->first();
                            if ($project->vk) {
                                $temp_task->targeting_to_vk = 1;
                                $text_to_user .= 'вконтакте,';
                            }
                            if ($project->ok) {
                                $temp_task->targeting_to_ok = 1;
                                $text_to_user .= 'одноклассники,';
                            }
                            if ($project->fb) {
                                $temp_task->targeting_to_fb = 1;
                                $text_to_user .= 'facebook,';
                            }
                            if ($project->insta) {
                                $temp_task->targeting_to_ig = 1;
                                $text_to_user .= 'instagram,';
                            }
                            if ($project->y_dzen) {
                                $temp_task->targeting_to_y_dzen = 1;
                                $text_to_user .= 'я.дзен,';
                            }
                            if ($project->y_street) {
                                $temp_task->targeting_to_y_street = 1;
                                $text_to_user .= 'я.улица,';
                            }
                            if ($project->yt) {
                                $temp_task->targeting_to_yt = 1;
                                $text_to_user .= 'youtube,';
                            }
                            if ($project->tg) {
                                $temp_task->targeting_to_tg = 1;
                                $text_to_user .= 'telegram,';
                            }

                            if ($count == 0) {
                                $this->telegram->sendMessage([
                                    'chat_id' => $user_id,
                                    'parse_mode' => 'HTML',
                                    'text' => 'В проекте нет привязанных соц сетей, поэтому выберите из списка.',
                                ]);
                                return 0;
                            }

                            $temp_task->save();
                            break;
                        case 1:
                            $temp_task->targeting_to_vk = 1;
                            $text_to_user .= "вконтакте, ";
                            break;
                        case 2:
                            $temp_task->targeting_to_ok = 1;
                            $text_to_user .= "одноклассники, ";
                            break;
                        case 3:
                            $temp_task->targeting_to_fb = 1;
                            $text_to_user .= "facebook, ";
                            break;
                        case 4:
                            $temp_task->targeting_to_ig = 1;
                            $text_to_user .= "instagram, ";
                            break;
                        case 5:
                            $temp_task->targeting_to_y_dzen = 1;
                            $text_to_user .= "я.дзен, ";
                            break;
                        case 6:
                            $temp_task->targeting_to_y_street = 1;
                            $text_to_user .= "я.район, ";
                            break;
                        case 7:
                            $temp_task->targeting_to_yt = 1;
                            $text_to_user .= "youtube, ";
                            break;
                        case 8:
                            $temp_task->targeting_to_tg = 1;
                            $text_to_user .= "telegram, ";
                            break;
                        default:
                            $text_to_user = "Ввели некорректные данные!";
                            $temp_task->targeting = 0;
                            $temp_task->save();
                            $this->telegram->sendMessage([
                                'chat_id' => $user_id,
                                'text' => $text_to_user,
                            ]);
                            return 0;
                            break;
                    }
                }

                $cache_user->forward_step = $steps[10];
                $cache_user->current_step = $steps[12];
                $cache_user->save();
                $temp_task->targeting_text = $comment;
                $temp_task->save();
                $text_to_user = Str::replaceLast(',', '', $text_to_user);
                $text_to_user .= "\n"
                    . "<b>Нужен посев?</b>";

                $keyboard = Keyboard::make()
                    ->inline()
                    ->row(Keyboard::inlineButton(['text' => 'Да', 'callback_data' => 'yes']))
                    ->row(Keyboard::inlineButton(['text' => 'Нет', 'callback_data' => 'no']))
                    ->row(Keyboard::inlineButton(['text' => 'Завершить постановку задачи', 'callback_data' => 'end_create']));
                $this->telegram->sendMessage([
                    'chat_id' => $user_id,
                    'parse_mode' => 'HTML',
                    'text' => $text_to_user,
                    'reply_markup' => $keyboard,

                ]);

                return 0;
                break;
            //

            case $steps[11]:
                /*
                $text_to_user = "Нужен посев?";
                $cache_user->forward_step = $steps[11];
                $cache_user->current_step = $steps[12];
                $cache_user->save();

                $keyboard = Keyboard::make()
                    ->inline()
                    ->row(Keyboard::inlineButton(['text' => 'Да' , 'callback_data' => 'yes']))
                    ->row(Keyboard::inlineButton(['text' => 'Нет', 'callback_data' => 'no']))
                    ->row(Keyboard::inlineButton(['text' => 'Завершить постановку задачи', 'callback_data' => 'end_create']));
                $this->telegram->sendMessage([
                    'chat_id' => $user_id,
                    'parse_mode' => 'HTML',
                    'text' => $text_to_user,
                    'reply_markup' => $keyboard,

                ]);
                */
                return 0;
                break;
            case $steps[12]:
                if (!empty($text))
                    return 0;

                $cache_user->forward_step = $steps[12];
                $cache_user->save();

                $take_action = $callback_data;
                foreach ($actions as $key => $value) {
                    $status_action = strpos($callback_data, $value);
                    if ($status_action !== false) {
                        $take_action = $value;
                    }
                }

                switch ($take_action) {
                    case $actions[0]:
                        $cache_user->current_step = $steps[13];
                        $cache_user->save();
                        $text = "Введите цифрами социальные сети: \n"
                            . "1 - Вконтакте \n"
                            . "2 - Одноклассники \n"
                            . "3 - Facebook \n"
                            . "4 - Instagram \n"
                            . "5 - Я.Дзен \n"
                            . "6 - Я.Район \n"
                            . "7 - Youtube \n"
                            . "8 - Telegram \n"
                            . "0 - Выбрать все соц-сети \n"
                            . "Пример: 123 Разместить в данных соц-сетях \n"
                            . "Напишите комментарий на второй строке \n";
                        $this->telegram->sendMessage([
                            'chat_id' => $user_id,
                            'text' => $text,
                        ]);
                        break;
                    case $actions[1]:
                        if ($temp_task->posting || $temp_task->seeding) {
                            $this->telegram->sendMessage([
                                'chat_id' => $user_id,
                                'text' => 'Нужно комментирование? 1 - Да 2 - Нет. На второй строчке напишите комментарий',
                            ]);
                            $cache_user->current_step = $steps[14];
                            $cache_user->save();
                        } else {
                            $post = new Post();
                            if ($post->journalist_id != null) {
                                $post->status_id = 1;
                            } else {
                                $post->status_id = 2;
                            }
                            $post->fill($temp_task->toArray());
                            $post->save();
                            $cache_user->forward_step = $cache_user->current_step;
                            $cache_user->current_step = null;
                            $cache_user->post_id = null;
                            $cache_user->save();
                            $temp_task->delete();
                            $this->telegram->sendMessage([
                                'chat_id' => $user_id,
                                'parse_mode' => 'HTML',
                                'text' => 'Задача <b>' . $temp_task->title . '</b> создана!',
                            ]);
                            $telegramBot->storeMessage($post);
                        }
                        break;
                    case $actions[2]:
                        $post = new Post();
                        $post->fill($temp_task->toArray());

                        if ($post->journalist_id != null) {
                            $post->status_id = 1;
                        } else {
                            $post->status_id = 2;
                        }

                        $post->save();
                        $info_new_task = '';
                        $formattText = $this->formatPostText($temp_task->text);
                        $deadline_post = $post->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';
                        (!is_null($temp_task->journalist_id)) ? $user_journalist = User::find($temp_task->journalist_id)->name : $user_journalist = 'Без назначения';
                        $info_new_task .= 'Задача <b>' . $temp_task->title . '</b> создана!' . "\n"
                            . '<b>Тезисы:  </b>' . "\n"
                            . $formattText . "\n"
                            . "Дедлайн: " . $deadline_post . "\n"
                            . '<b>Исполнитель: </b>' . $user_journalist . "\n";

                        if ($temp_task->posting) {
                            $info_new_task .= '<b>Соц-сети:</b> ';
                            ($temp_task->posting_to_vk) ? $info_new_task .= 'ВК, ' : '';
                            ($temp_task->posting_to_ok) ? $info_new_task .= 'OK, ' : '';
                            ($temp_task->posting_to_fb) ? $info_new_task .= 'FB, ' : '';
                            ($temp_task->posting_to_ig) ? $info_new_task .= 'IG, ' : '';
                            ($temp_task->posting_to_y_dzen) ? $info_new_task .= 'Я.Дзен, ' : '';
                            ($temp_task->posting_to_y_street) ? $info_new_task .= 'Я.Районы, ' : '';
                            ($temp_task->posting_to_yt) ? $info_new_task .= 'YT, ' : '';
                            ($temp_task->posting_to_tt) ? $info_new_task .= 'TT, ' : '';
                            ($temp_task->posting_to_tg) ? $info_new_task .= 'TG, ' : '';

                            $info_new_task = Str::replaceLast(', ', '', $info_new_task);
                            $info_new_task .= "\n";


                            if ($temp_task->targeting) {
                                $info_new_task .= "<b>Тарегтированная реклама:</b> Да \n";

                            }

                            if ($temp_task->seeding) {
                                $info_new_task .= "<b>Посевы:</b> Да \n";
                            }

                            if ($temp_task->commenting) {
                                $info_new_task .= "<b>Комментирование:</b> Да  \n";

                            }

                        }

                        $info_new_task .= "<b>Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a></b> \n";

                        $cache_user->forward_step = $cache_user->current_step;
                        $cache_user->current_step = null;
                        $cache_user->post_id = null;
                        $cache_user->save();
                        $this->telegram->sendMessage([
                            'chat_id' => $user_id,
                            'parse_mode' => 'HTML',
                            'text' => $info_new_task,
                        ]);
                        $temp_task->delete();
                        $telegramBot->storeMessage($post);
                        break;
                }
                return 0;
                break;
            case $steps[13]:
                if (!empty($callback_data)) {
                    $this->telegram->sendMessage([
                        'chat_id' => $user_id,
                        'text' => 'Выберите соц-сети!',
                    ]);
                    return 0;
                }

                $temp_task->seeding = 1;
                $regular = "/[\s,]+/";
                $text = trim($text);
                $message = preg_split($regular, $text);
                $take_social_seeding = $message[0];
                unset($message[0]);
                $comment = implode(" ", $message);


                $text_to_user = "Вы выбрали соц-сети: ";
                for ($i = 0; $i < Str::length($take_social_seeding); $i++) {
                    switch ($take_social_seeding[$i]) {
                        case 0:
                            $project = Project::all()->where('id', $temp_task->project_id)->first();
                            $temp_task->seeding_to_vk = 1;
                            $temp_task->seeding_to_ok = 1;
                            $temp_task->seeding_to_fb = 1;
                            $temp_task->seeding_to_insta = 1;
                            $temp_task->seeding_to_y_dzen = 1;
                            $temp_task->seeding_to_y_street = 1;
                            $temp_task->seeding_to_yt = 1;
                            $temp_task->seeding_to_tg = 1;
                            $temp_task->save();
                            $text_to_user = Str::replaceLast(',', '', $text_to_user);
                            $text_to_user .= 'Выбраны все соц-сети';
                            break;
                        case 1:
                            $temp_task->seeding_to_vk = 1;
                            $text_to_user .= "вконтакте, ";
                            break;
                        case 2:
                            $temp_task->seeding_to_ok = 1;
                            $text_to_user .= "одноклассники, ";
                            break;
                        case 3:
                            $temp_task->seeding_to_fb = 1;
                            $text_to_user .= "facebook, ";
                            break;
                        case 4:
                            $temp_task->seeding_to_insta = 1;
                            $text_to_user .= "instagram, ";
                            break;
                        case 5:
                            $temp_task->seeding_to_y_dzen = 1;
                            $text_to_user .= "я.дзен, ";
                            break;
                        case 6:
                            $temp_task->seeding_to_y_street = 1;
                            $text_to_user .= "я.район, ";
                            break;
                        case 7:
                            $temp_task->seeding_to_yt = 1;
                            $text_to_user .= "youtube, ";
                            break;
                        case 8:
                            $temp_task->seeding_to_tg = 1;
                            $text_to_user .= "telegram, ";
                            break;
                        default:
                            $text_to_user = "Ввели некорректные данные!";
                            $text_to_user = Str::replaceLast(',', '', $text_to_user);
                            $temp_task->seeding = 0;
                            $temp_task->save();
                            $this->telegram->sendMessage([
                                'chat_id' => $user_id,
                                'text' => $text_to_user,
                            ]);
                            return 0;
                            break;
                    }
                }

                $cache_user->forward_step = $steps[13];
                $cache_user->current_step = $steps[15];
                $cache_user->save();
                $temp_task->seeding_text = $comment;
                $temp_task->save();

                $text_to_user .= "\n"
                    . "<b>Нужно комментирование? 1 - Да 2 - Нет. На второй строчке напишите комментарий</b>";

                $cache_user->current_step = $steps[14];
                $cache_user->save();
                $this->telegram->sendMessage([
                    'chat_id' => $user_id,
                    'parse_mode' => 'HTML',
                    'text' => $text_to_user,


                ]);

                return 0;
                break;
            case $steps[14]:
                if (!empty($callback_data)) {
                    $this->telegram->sendMessage([
                        'chat_id' => $user_id,
                        'text' => 'Неверное действие!',
                    ]);
                    return 0;
                }

                $regular = "/[\s,]+/";
                $text = trim($text);
                $message = preg_split($regular, $text);
                $take_commenting = $message[0];

                if ($take_commenting == 1) {
                    unset($message[0]);
                    $comment = implode(" ", $message);
                    $cache_user->forward_step = $steps[14];
                    $temp_task->commenting = 1;
                    $temp_task->commenting_text = $comment;
                    $cache_user->forward_step = $cache_user->current_step;
                    $cache_user->current_step = null;
                    $cache_user->post_id = null;
                    $cache_user->save();
                    $temp_task->save();


                    $post = new Post();
                    $post->fill($temp_task->toArray());

                    if ($post->journalist_id != null) {
                        $post->status_id = 1;
                    } else {
                        $post->status_id = 2;
                    }

                    $deadline_post = $post->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';
                    $post->save();
                    $info_new_task = '';
                    $formattText = $this->formatPostText($temp_task->text);
                    (!is_null($temp_task->journalist_id)) ? $user_journalist = User::find($temp_task->journalist_id)->name : $user_journalist = 'Без назначения';
                    $info_new_task .= 'Задача <b>' . $temp_task->title . '</b> создана!' . "\n"
                        . '<b>Тезисы:  </b>' . "\n"
                        . $formattText . "\n"
                        . "Дедлайн: " . $deadline_post . "\n"
                        . '<b>Исполнитель: </b>' . $user_journalist . "\n";

                    if ($temp_task->posting) {
                        $info_new_task .= '<b>Соц-сети:</b> ';
                        ($temp_task->posting_to_vk) ? $info_new_task .= 'ВК, ' : '';
                        ($temp_task->posting_to_ok) ? $info_new_task .= 'OK, ' : '';
                        ($temp_task->posting_to_fb) ? $info_new_task .= 'FB, ' : '';
                        ($temp_task->posting_to_ig) ? $info_new_task .= 'IG, ' : '';
                        ($temp_task->posting_to_y_dzen) ? $info_new_task .= 'Я.Дзен, ' : '';
                        ($temp_task->posting_to_y_street) ? $info_new_task .= 'Я.Районы, ' : '';
                        ($temp_task->posting_to_yt) ? $info_new_task .= 'YT, ' : '';
                        ($temp_task->posting_to_tt) ? $info_new_task .= 'TT, ' : '';
                        ($temp_task->posting_to_tg) ? $info_new_task .= 'TG, ' : '';

                        $info_new_task = Str::replaceLast(', ', '', $info_new_task);
                        $info_new_task .= "\n";


                        if ($temp_task->targeting) {
                            $info_new_task .= "<b>Тарегтированная реклама:</b> Да \n";

                        }

                        if ($temp_task->seeding) {
                            $info_new_task .= "<b>Посевы:</b> Да \n";
                        }

                        if ($temp_task->commenting) {
                            $info_new_task .= "<b>Комментирование:</b> Да  \n";

                        }

                    }

                    $info_new_task .= "<b>Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a></b> \n";

                    $temp_task->delete();
                    $this->telegram->sendMessage([
                        'chat_id' => $user_id,
                        'parse_mode' => 'HTML',
                        'text' => $info_new_task,

                    ]);
                    $telegramBot->storeMessage($post);
                } else {
                    $cache_user->forward_step = $cache_user->current_step;
                    $cache_user->current_step = null;
                    $cache_user->post_id = null;
                    $cache_user->save();
                    $post = new Post();
                    $post->fill($temp_task->toArray());

                    if ($post->journalist_id != null) {
                        $post->status_id = 1;
                    } else {
                        $post->status_id = 2;
                    }

                    $post->save();
                    $info_new_task = '';
                    $formattText = $this->formatPostText($temp_task->text);
                    $deadline_post = $post->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';
                    (!is_null($temp_task->journalist_id)) ? $user_journalist = User::find($temp_task->journalist_id)->name : $user_journalist = 'Без назначения';
                    $info_new_task .= 'Задача <b>' . $temp_task->title . '</b> создана!' . "\n"
                        . '<b>Тезисы:  </b>' . "\n"
                        . $formattText . "\n"
                        . "Дедлайн: " . $deadline_post . "\n"
                        . '<b>Исполнитель: </b>' . $user_journalist . "\n";

                    if ($temp_task->posting) {
                        $info_new_task .= '<b>Соц-сети:</b> ';
                        ($temp_task->posting_to_vk) ? $info_new_task .= 'ВК, ' : '';
                        ($temp_task->posting_to_ok) ? $info_new_task .= 'OK, ' : '';
                        ($temp_task->posting_to_fb) ? $info_new_task .= 'FB, ' : '';
                        ($temp_task->posting_to_ig) ? $info_new_task .= 'IG, ' : '';
                        ($temp_task->posting_to_y_dzen) ? $info_new_task .= 'Я.Дзен, ' : '';
                        ($temp_task->posting_to_y_street) ? $info_new_task .= 'Я.Районы, ' : '';
                        ($temp_task->posting_to_yt) ? $info_new_task .= 'YT, ' : '';
                        ($temp_task->posting_to_tt) ? $info_new_task .= 'TT, ' : '';
                        ($temp_task->posting_to_tg) ? $info_new_task .= 'TG, ' : '';

                        $info_new_task = Str::replaceLast(', ', '', $info_new_task);
                        $info_new_task .= "\n";


                        if ($temp_task->targeting) {
                            $info_new_task .= "<b>Тарегтированная реклама:</b> Да \n";

                        }

                        if ($temp_task->seeding) {
                            $info_new_task .= "<b>Посевы:</b> Да \n";
                        }

                        if ($temp_task->commenting) {
                            $info_new_task .= "<b>Комментирование:</b> Да  \n";

                        }

                    }

                    $info_new_task .= "<b>Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a></b> \n";

                    $temp_task->delete();
                    $this->telegram->sendMessage([
                        'chat_id' => $user_id,
                        'parse_mode' => 'HTML',
                        'text' => $info_new_task

                    ]);
                    $telegramBot->storeMessage($post);
                }

                return 0;
                break;
        }

        return 0;
    }


    /**
     * Здесь собранны уведомления для редактора в ТГ после работы с задачами журналистом, смм, посевом и тд.
     * @param $post_id
     * @param $call
     * @param null $comments_commentator
     * @return int
     */
    public function NotificationEditor($post_id, $call, $comments_commentator = null)
    {
        $calls = ['journalist_take_task', 'journalist_give_draft_url', 'journalist_give_publication_url', 'smm_send_links', 'target_in_moderation', 'seeder_send_link', 'commenter_send_comments', 'seeder_commercial_send_link'];
        $post = Post::all()->where('id', $post_id)->first();
        $editor = User::all()->where('id', $post->editor->id)->first();
        $count_posts = Post::all()->whereNull('archived_at')->where('editor_id', $post->editor_id)->count();


        if ($post->status_task === null && $post->journalist_id === null)
            $status = 'Без назначения';
        elseif ($post->status_task === null && $post->journalist_id != null)
            $status = 'не в работе';
        elseif ($post->publication_url)
            $status = 'Опубликовано';
        elseif ($post->approved)
            $status = 'Ждет публикации';
        elseif ($post->draft_url && $post->approved === null)
            $status = 'Нужна проверка';
        elseif ($post->draft_url && !$post->approved)
            $status = 'На доработке';
        elseif ($post->hasJournalist())
            $status = 'В работе';
        else
            $status = 'Не в работе';


        $status = ltrim($status);

        $deadline_post = $post->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';
        $text_for_editor = '';
        switch ($call) {
            case $calls[0]:
                // Отправление уведомление редактору насчет взятия задачи в работу
                $formattText = $this->formatPostText($post->text);
                $text_for_editor = "<b>Добавился ответственный к задаче</b>\n"
                    . "$post->id - $post->title\n"
                    . "<b>Исполнитель: </b>" . $post->journalist->name . "\n"
                    . "Тезисы: \n"
                    . $formattText . "\n"
                    . "<b>Дедлайн: </b>" . $deadline_post . "\n"
                    . "Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a>\n"
                    . "<b> Статус задачи: </b>" . $status;

                $text_for_editor = $this->formatPostText($text_for_editor);
                $keyboard = Keyboard::make()
                    ->inline();
                $keyboard->row(
                    Keyboard::inlineButton(['text' => 'Список задач (' . $count_posts . ')', 'callback_data' => 'take_tasks']));
                break;
            case $calls[1]:
                // Отправление уведомление редактору после отправки ссылки на материал журналистом
                $editor = User::all()->where('id', $post->editor->id)->first();

                $comments = '';
                $journalist_text = '';
                $editor_text = '';

                $array_comments = array_reverse($post->comments->toArray());
                foreach ($array_comments as $value) {
                    if ($value['role'] == 'Журналист') {
                        $comments .= "<b>Комментарий журналиста: </b>" . $value['text'] . "\n";
                    } else {
                        $comments .= "<b>Комментарий редактора: </b>" . $value['text'] . "\n";
                    }
                }


                $formattText = $this->formatPostText($post->text);
                $comments = $this->formatPostText($comments);

                $text_for_editor = "<b>Задача требует проверки</b>\n"
                    . "$post->id - $post->title\n"
                    . "<b>Исполнитель:</b> " . $post->journalist->name . "\n"
                    . "<b>Тезисы:</b> \n"
                    . $formattText . "\n"
                    . "<b>Дедлайн: </b>" . $deadline_post . "\n"
                    . $comments
                    . "<b>Ссылка на материал: </b><a href='" . $post->draft_url . "'>" . $post->draft_url . "</a> \n"
                    . "<b>Вы можете открыть задачу, перейдя по ссылке: </b><a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a>\n"
                    . "<b>Статус задачи: </b>" . $status;
                $text_for_editor = $this->formatPostText($text_for_editor);

                $keyboard = Keyboard::make()
                    ->inline()
                    ->row(
                        Keyboard::inlineButton(['text' => 'Принять материал', 'callback_data' => 'accept_draft_url_' . $post->id]))
                    ->row(
                        Keyboard::inlineButton(['text' => 'Отправить на доработку', 'callback_data' => 'go_to_moderating_' . $post->id]));
                break;
            case $calls[2]:
                // Отправление уведомление редактору после отправки публикации на материал журналистом
                $editor = User::all()->where('id', $post->editor->id)->first();
                $formattText = $this->formatPostText($post->text);
                $text_for_editor = "<b>Материал опубликован</b>\n"
                    . "$post->id - $post->title\n"
                    . "<b>Исполнитель:</b> " . $post->journalist->name . "\n"
                    . "<b>Тезисы: </b>\n"
                    . $formattText . "\n"
                    . "<b>Дедлайн: </b>" . $deadline_post . "\n"
                    . "<b>Ссылка на публикацию: </b><a href='" . $post->publication_url . "'>" . $post->publication_url . "</a> \n"
                    . "<b>Вы можете открыть задачу, перейдя по ссылке: </b><a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a>\n"
                    . "<b>Статус задачи: </b>" . $status;
                $text_for_editor = $this->formatPostText($text_for_editor);
                $keyboard = Keyboard::make()
                    ->inline();
                $keyboard->row(
                    Keyboard::inlineButton(['text' => 'Список задач (' . $count_posts . ')', 'callback_data' => 'take_tasks']));
                break;
            case $calls[3]:
                // Отправление уведомление редактору после размещения в соц-сетяъ


                $editor = User::all()->where('id', $post->editor->id)->first();
                $formattText = $this->formatPostText($post->text);

                $social_networks = "<b>Соц-ceти: </b>\n";

                foreach(SmmLink::where('post_id', $post->id)->get() as $link) {
                    $socialNetwork_name = SocialNetwork::find($link->social_network_id)->name;
                    $social_networks .= 'Ссылка на ' . $socialNetwork_name .': <a href="' . $link->link . '">' . $link->link . '</a>' . "\n";
                }



                $social_networks = trim($social_networks);
                $social_networks = $this->formatPostText($social_networks);


                $text_for_editor = "<b>Публикация была размещена в соц сетях</b>\n"
                    . "$post->id - $post->title\n"
                    . "<b>Исполнитель:</b> " . $post->smm->name . "\n"
                    . "<b>Тезисы: </b>\n"
                    . $formattText . "\n"
                    . "<b>Дедлайн: </b>" . $deadline_post . "\n"
                    . $social_networks . "\n"
                    . "<b>Вы можете открыть задачу, перейдя по ссылке: </b><a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a>\n"
                    . "<b>Статус задачи: </b>" . $status;
                $text_for_editor = $this->formatPostText($text_for_editor);
                $text_for_editor = rtrim($text_for_editor, ',');
                $keyboard = Keyboard::make()
                    ->inline();
                $keyboard->row(
                    Keyboard::inlineButton(['text' => 'Список задач (' . $count_posts . ')', 'callback_data' => 'take_tasks']));
                break;
            case $calls[4]:
                // Отправление уведомление редактору после таргетинга в соц-сети


                $editor = User::all()->where('id', $post->editor->id)->first();
                $formattText = $this->formatPostText($post->text);

                $social_networks = "<b>Соц-сети: </b>\n";

                $socialNetworks = $post->socialNetworks()->wherePivot('status', '=', PostTargetStatusesEnum::SENT_FOR_MODERATION_STATUS)->orWherePivot('status', '=', PostTargetStatusesEnum::SUCCESSFUL_MODERATED_STATUS)->get();
                if ($socialNetworks->count() < 0) {
                    return 0;
                }

                foreach ($socialNetworks as $socialNetwork) {
                    $postUrl = $socialNetwork->slug . "_post_url";
                    $social_networks .= "Ссылка на {$socialNetwork->name}: <a href=\"{$post->$postUrl}\">{$post->$postUrl}</a>\n";
                }

                $social_networks = trim($social_networks);
                $social_networks = $this->formatPostText($social_networks);

                $text_for_editor = "<b>Таргетированная реклама была отправлена на модерацию</b>\n"
                    . "$post->id - $post->title\n"
                    . "Исполнитель: " . $post->target->name . "\n"
                    . "<b>Тезисы: </b>\n"
                    . $formattText . "\n"
                    . "<b>Дедлайн: </b>" . $deadline_post . "\n"
                    . $social_networks . "\n"
                    . "<b>Вы можете открыть задачу, перейдя по ссылке: </b><a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a>\n"
                    . "<b>Статус задачи: </b>" . $status;
                $text_for_editor = $this->formatPostText($text_for_editor);
                $text_for_editor = rtrim($text_for_editor, ',');
                $keyboard = Keyboard::make()
                    ->inline();
                $keyboard->row(
                    Keyboard::inlineButton(['text' => 'Список задач (' . $count_posts . ')', 'callback_data' => 'take_tasks']));
                break;
            case $calls[5]:


                $editor = User::all()->where('id', $post->editor->id)->first();
                $formattText = $this->formatPostText($post->text);

                $social_networks = "<b>Соц-сети: </b> \n";

                foreach(SmmLink::where('post_id', $post->id)->get() as $link) {
                    $socialNetwork_name = SocialNetwork::find($link->social_network_id)->name;
                    $social_networks .= 'Ссылка на ' . $socialNetwork_name .': <a href="' . $link->link . '">' . $link->link . '</a>' . "\n";
                }


                $social_networks = trim($social_networks);
                $social_networks = $this->formatPostText($social_networks);

                $text_for_editor = "<b>Были сделаны посевы</b>\n"
                    . "$post->id - $post->title\n"
                    . "Исполнитель: " . $post->seeder->name . "\n"
                    . "<b>Тезисы: </b>\n"
                    . $formattText . "\n"
                    . "<b>Дедлайн: </b>" . $deadline_post . "\n"
                    . $social_networks . "\n"
                    . "<b>Ссылка на документ с посевом: </b><a href='" . $post->seed_list_url . "'>" . $post->seed_list_url . "</a> \n"
                    . "<b>Вы можете открыть задачу, перейдя по ссылке: </b><a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a>\n"
                    . "<b>Статус задачи: </b>" . $status;
                $text_for_editor = $this->formatPostText($text_for_editor);
                $text_for_editor = rtrim($text_for_editor, ',');
                $keyboard = Keyboard::make()
                    ->inline();
                $keyboard->row(
                    Keyboard::inlineButton(['text' => 'Список задач (' . $count_posts . ')', 'callback_data' => 'take_tasks']));
                break;
            case $calls[6]:
                // Отправление уведомление редактору после отправки ссылки на материал журналистом
                $editor = User::all()->where('id', $post->editor->id)->first();
                $formattText = $this->formatPostText($post->text);

                $text_for_editor = "<b>Комментарии были добавлены к задаче</b>\n"
                    . "$post->id - $post->title\n"
                    . "<b>Исполнитель:</b> " . $post->commentator->name . "\n"
                    . "<b>Тезисы: </b>\n"
                    . $formattText . "\n"
                    . "<b>Дедлайн: </b>" . $deadline_post . "\n"
                    . $comments_commentator
                    . "<b>Вы можете открыть задачу, перейдя по ссылке: </b><a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a>\n"
                    . "<b>Статус задачи: </b>" . $status;

                $text_for_editor = $this->formatPostText($text_for_editor);
                $keyboard = Keyboard::make()
                    ->inline();
                $keyboard->row(
                    Keyboard::inlineButton(['text' => 'Список задач (' . $count_posts . ')', 'callback_data' => 'take_tasks']));
                break;
            case $calls[7]:


                $editor = User::all()->where('id', $post->editor->id)->first();
                $formattText = $this->formatPostText($post->text);

                $social_networks = "<b>Соц-сети: </b> \n";

                foreach(ModelsSeedLinks::where('post_id', $post->id)->get() as $link) {
                    $socialNetwork_name = SocialNetwork::find($link->social_network_id)->name;
                    $social_networks .= 'Ссылка на ' . $socialNetwork_name .': <a href="' . $link->link . '">' . $link->link . '</a>' . "\n";
                }


                $social_networks = trim($social_networks);
                $social_networks = $this->formatPostText($social_networks);

                $text_for_editor = "<b>Были сделаны коммерческие выходы</b>\n"
                    . "$post->id - $post->title\n"
//                    . "Исполнитель: " . $post->seeder->name . "\n"
                    . "<b>Тезисы: </b>\n"
                    . $formattText . "\n"
                    . "<b>Дедлайн: </b>" . $deadline_post . "\n"
                    . $social_networks . "\n"
                    . "<b>Ссылка на документ с посевом: </b><a href='" . $post->seed_list_url . "'>" . $post->seed_list_url . "</a> \n"
                    . "<b>Вы можете открыть задачу, перейдя по ссылке: </b><a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a>\n"
                    . "<b>Статус задачи: </b>" . $status;
                $text_for_editor = $this->formatPostText($text_for_editor);
                $text_for_editor = rtrim($text_for_editor, ',');
                $keyboard = Keyboard::make()
                    ->inline();
                $keyboard->row(
                    Keyboard::inlineButton(['text' => 'Список задач (' . $count_posts . ')', 'callback_data' => 'take_tasks']));
                break;
        }

        (new TelegramBotController(new Api()))->sendMessageToOneUser($editor->telegram_id, $text_for_editor, $keyboard);
    }


    public function EditorWorkWithTask($callback_data = null, $user_id, $text = null)
    {

        $telegramBot = new TelegramBot($this->telegram);
        $user = User::all()->where('telegram_id', $user_id)->first();
        $cache_user = Cache::all()->where('telegram_id', $user_id)->first();


        // список колбеков инлайновых клавиатур
        $callbacks = ['go_to_moderating_', 'accept_draft_url_'];

        if (strpos($callback_data, $callbacks[0]) !== false) {

            $text_to_editor = 'Добавьте комментарий';
            $this->telegram->sendMessage([
                'chat_id' => $user_id,
                'parse_mode' => 'HTML',
                'text' => $text_to_editor,
            ]);

            $take_id_post = $callback_data;
            $take_id_post = str_replace($callbacks[0], "", $take_id_post);

            $cache_user->current_step = "wait_moderating_comment";
            $cache_user->forward_step = "add_task_to_moderate";

            $cache_user->post_id = $take_id_post;
            $post = Post::all()->where('id', $take_id_post)->first();

            $post->approved = false;
            $post->on_moderate = true;
            $post->status_id = 5;
            $post->save();
            $cache_user->save();
        }

        if (strpos($callback_data, $callbacks[1]) !== false) {
            $take_id_post = $callback_data;
            $take_id_post = str_replace($callbacks[1], "", $take_id_post);
            $post = Post::all()->where('id', $take_id_post)->first();

            $post->approved = true;
            $post->on_moderate = false;
            $post->status_id = 6;
            $post->save();

            $count_posts = Post::all()->whereNull('archived_at')->where('editor_id', $post->editor_id)->count();

            $keyboard = Keyboard::make()
                ->inline();
            $keyboard->row(
                Keyboard::inlineButton(['text' => 'Список задач (' . $count_posts . ')', 'callback_data' => 'take_tasks']));

            $text_to_editor = 'Материал по задаче <b>' . $post->id . ' - ' . $post->title . '</b> принят, ожидается ссылка на публикацию.'
                . " Посмотреть задачу: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a>";


            $text_to_editor = $this->formatPostText($text_to_editor);

            $telegramBot->storeMessageModerateApprove($post);
            $this->telegram->sendMessage([
                'chat_id' => $user_id,
                'parse_mode' => 'HTML',
                'text' => $text_to_editor,
                'reply_markup' => $keyboard
            ]);
        }

        if (!is_null($text) && $cache_user->current_step == 'wait_moderating_comment') {
            $post = Post::all()->where('id', $cache_user->post_id)->first();

            $comment = new comment([
                'text' => $text,
                'role' => 'Главный редактор',
                'post_id' => $post->id,
            ]);
            $comment->save();

            $count_posts = Post::all()->where('editor_id', $post->editor_id)->count();

            $text_to_editor = 'Комментарий добавлен и задача <b>' . $post->id . ' - ' . $post->title . '</b> отправлена на доработку.'
                . " Посмотреть задачу: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a>";
            $keyboard = Keyboard::make()
                ->inline();
            $keyboard->row(
                Keyboard::inlineButton(['text' => 'Список задач (' . $count_posts . ')', 'callback_data' => 'take_tasks']));


            $text_to_editor = $this->formatPostText($text_to_editor);

            $telegramBot->storeMessageModerateRework($post);

            $this->telegram->sendMessage([
                'chat_id' => $user_id,
                'parse_mode' => 'HTML',
                'text' => $text_to_editor,
                'reply_markup' => $keyboard
            ]);


        }

        return 'Ok';
    }

    public function getTasksEditor($defaultKeyboard, $inlineKeyboard, $user_id, $callbackData = null, $status_task = null, $current_page = null)
    {
        $user = User::all()->where('telegram_id', $user_id)->first();
        $status = ['status_not_journalist', 'status_not_work', 'status_in_work', 'status_wait_check', 'status_wait_public', 'status_wait_posting_to_social',
            'status_wait_target_to_social', 'status_wait_seed', 'status_wait_comment'];

        $keyboard = Keyboard::make()
            ->inline();
        /* создание кнопок клавиатур
            короткая запись if else
         */
        (Post::all()->where('journalist_id', null)->where('archived_at', null)->where('status_task', 0)->where('editor_id', $user->id)->count()) ? $keyboard->row(Keyboard::inlineButton(['text' => 'Задачи без назначения (' . Post::all()->where('journalist_id', 0)->where('archived_at', null)->where('status_task', 0)->where('editor_id', $user->id)->count() . ')  ', 'callback_data' => 'status_not_journalist',])
        ) : false;

        (Post::all()->whereNotNull('journalist_id')->where('archived_at', null)->where('status_task', null)->where('editor_id', $user->id)->count()) ? $keyboard->row(Keyboard::inlineButton(['text' => 'Задачи не в работе (' . Post::all()->whereNotNull('journalist_id')->where('archived_at', null)->where('status_task', null)->where('editor_id', $user->id)->count() . ')  ', 'callback_data' => 'status_not_work'])
        ) : false;

        (Post::all()->where('archived_at', null)->where('status_task', 1)->where('draft_url', null)->where('editor_id', $user->id)->count()) ? $keyboard->row(Keyboard::inlineButton(['text' => 'Задачи в работе (' . Post::all()->where('archived_at', null)->where('status_task', 1)->where('draft_url', null)->where('editor_id', $user->id)->count() . ')', 'callback_data' => 'status_in_work'])
        ) : false;

        (Post::all()->where('archived_at', null)->where('draft_url', '!=', null)->where('approved', null)->where('editor_id', $user->id)->where('on_moderate', 0)->count()) ? $keyboard->row(Keyboard::inlineButton(['text' => 'Требуют проверки (' . Post::where('archived_at', null)->where('draft_url', '!=', null)->whereNull('publication_url')->where('approved', null)->where('on_moderate', 0)->where('editor_id', $user->id)->count() . ')', 'callback_data' => 'status_wait_check'])
        ) : false;

        (Post::all()->whereNull('archived_at')->where('approved', 1)->whereNull('publication_url')->where('editor_id', $user->id)->count()) ? $keyboard->row(Keyboard::inlineButton(['text' => 'Ожидает публикации (' . Post::all()->where('archived_at', null)->where('approved', 1)->where('publication_url', null)->where('editor_id', $user->id)->count() . ')', 'callback_data' => 'status_wait_public'])
        ) : false;

        (Post::all()->where('archived_at', null)->whereNotNull('publication_url')->where('posting', 1)->where('smm_id', 0)->where('editor_id', $user->id)->count()) ? $keyboard->row(Keyboard::inlineButton(['text' => 'Ожидают размещение в соц сетях (' . Post::all()->where('archived_at', null)->whereNotNull('publication_url')->where('posting', 1)->where('smm_id', 0)->where('editor_id', $user->id)->count() . ')', 'callback_data' => 'status_wait_posting_to_social'])
        ) : false;


        (Post::all()->where('archived_at', null)->where('posting', 1)->where('smm_id', '>', 0)->where('targeting', 1)->where('targeter_id', 0)->where('editor_id', $user->id)->count()) ? $keyboard->row(Keyboard::inlineButton(['text' => 'Ожидают таргетированную рекламу (' . Post::all()->where('archived_at', null)->where('posting', 1)->where('smm_id', '>', 0)->where('targeting', 1)->where('targeter_id', 0)->where('editor_id', $user->id)->count() . ')', 'callback_data' => 'status_wait_target_to_social'])
        ) : false;

        (Post::all()->where('archived_at', null)->where('posting', 1)->where('smm_id', '>', 0)->where('seeding', 1)->where('seeder_id', 0)->where('editor_id', $user->id)->count()) ? $keyboard->row(Keyboard::inlineButton(['text' => 'Ожидают посев (' . Post::all()->where('archived_at', null)->where('posting', 1)->where('smm_id', '>', 0)->where('seeding', 1)->where('seeder_id', 0)->where('editor_id', $user->id)->count() . ')', 'callback_data' => 'status_wait_seed'])
        ) : false;

        (Post::all()->where('archived_at', null)->where('posting', 1)->where('smm_id', '>', 0)->where('commenting', 1)->where('commentator_id', 0)->where('editor_id', $user->id)->count()) ? $keyboard->row(Keyboard::inlineButton(['text' => 'Ожидают комментирование (' . Post::all()->where('archived_at', null)->where('posting', 1)->where('smm_id', '>', 0)->where('commenting', 1)->where('commentator_id', 0)->where('editor_id', $user->id)->count() . ')', 'callback_data' => 'status_wait_comment'])
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

            // Зря ты сюда полез...я недоволен этим функционалом
            if ($inlineKeyboard) {

                if ($status_task !== null) {
                    $callbackData = $status[$status_task];
                }

                $count = 1;
                switch ($callbackData) {
                    case $status[0]:
                        $from = 0;
                        if ($current_page && $current_page != 1) {
                            $posts = Post::where('journalist_id', null)->where('archived_at', null)->where('status_task', null)->where('editor_id', $user->id)->orderBy('expires_at', 'ASC')->paginate(($current_page + 1) * 5);

                            if (5 * $current_page < $posts->count())
                                $from = $posts->count() - 5;
                            else
                                $from = ($current_page - 1) * 5;

                            $count_posts = $posts->count();
                            $count = (($current_page * 5) - 5) + 1;
                        } else {
                            $posts = Post::where('journalist_id', null)->where('archived_at', null)->where('status_task', null)->where('editor_id', $user->id)->orderBy('expires_at', 'ASC')->paginate(5);
                            $from = 0;
                            $count_posts = $posts->count();
                            $count = 1;
                        }


                        $text = "<b>Без назначения: </b> \n";
                        for ($from; $from < $count_posts; $from++) {
//                           $deadline_post = ($posts[$from]->expired()) ? '-' . $posts[$from]->date_offset : $posts[$from]->date_offset;

                            $deadline_post = $posts[$from]->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';
                            $detail_post = new MessageEntity([
                                'type' => 'bot_command',
                                'offset' => 8,
                                'length' => 10,
                            ]);
                            $detail_post['url'] = '/post' . $posts[$from]->id;
                            $url = $detail_post->get('url');
                            $text .=
                                "Задача № " . $count . ' (' . $posts[$from]->id . ") \n"
                                . "<b>Название задачи: </b> " . $posts[$from]->title . " \n"
                                . "<b>Дедлайн: </b> " . $deadline_post . "\n"
                                . "<b>Подробнее: </b>" . $url . "\n"
                                . " \n";

                            $count++;
                        }
                        if (empty($text)) {
                            $text = 'Пусто';
                        }

                        $count_posts = Post::where('journalist_id', null)->where('archived_at', null)->where('editor_id', $user->id)->where('status_task', null)->count();
                        $status_task = ($status_task) ? $status_task : 0;
                        $pagination = $this->TasksPagination($current_page, $count_posts, $status_task);
                        $this->telegram->sendMessage([
                            'chat_id' => $user_id,
                            'parse_mode' => 'HTML',
                            'text' => $text,
                            'reply_markup' => json_encode([
                                'inline_keyboard' => [
                                    $pagination['keyboard'],
                                ],
                            ]),
                        ]);

                        break;
                    case $status[1]:
                        $from = 0;
                        if ($current_page && $current_page != 1) {
                            $posts = Post::whereNotNull('journalist_id')->where('archived_at', null)->where('status_task', null)->where('editor_id', $user->id)->orderBy('expires_at', 'ASC')->paginate(($current_page + 1) * 5);

                            if (5 * $current_page < $posts->count())
                                $from = $posts->count() - 5;
                            else
                                $from = ($current_page - 1) * 5;

                            $count_posts = $posts->count();
                            $count = (($current_page * 5) - 5) + 1;

                        } else {
                            $posts = Post::whereNotNull('journalist_id')->where('archived_at', null)->where('status_task', null)->where('editor_id', $user->id)->orderBy('expires_at', 'ASC')->paginate(5);
                            $from = 0;
                            $count_posts = $posts->count();
                            $count = 1;
                        }

                        $text = "<b>Не в работе: </b> \n";
                        for ($from; $from < $count_posts; $from++) {
                            $deadline_post = $posts[$from]->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';
                            $detail_post = new MessageEntity([
                                'type' => 'bot_command',
                                'offset' => 8,
                                'length' => 10,
                            ]);
                            $detail_post['url'] = '/post' . $posts[$from]->id;
                            $url = $detail_post->get('url');
                            $text .=
                                "Задача № " . $count . ' (' . $posts[$from]->id . ") \n"
                                . "<b>Название задачи: </b> " . $posts[$from]->title . " \n"
                                . "<b>Дедлайн: </b> " . $deadline_post . "\n"
                                . "<b>Подробнее: </b>" . $url . "\n"
                                . " \n";

                            $count++;
                        }
                        if (empty($text)) {
                            $text = 'Пусто';
                        }

                        $count_posts = Post::whereNotNull('journalist_id')->where('archived_at', null)->where('editor_id', $user->id)->where('status_task', null)->count();
                        $status_task = ($status_task) ? $status_task : 1;
                        $pagination = $this->TasksPagination($current_page, $count_posts, $status_task);
                        $this->telegram->sendMessage([
                            'chat_id' => $user_id,
                            'parse_mode' => 'HTML',
                            'text' => $text,
                            'reply_markup' => json_encode([
                                'inline_keyboard' => [
                                    $pagination['keyboard'],
                                ],
                            ]),
                        ]);
                        break;
                    case $status[2]:

                        $from = 0;
                        if ($current_page && $current_page != 1) {
                            $posts = Post::where('archived_at', null)->where('status_task', 1)->where('draft_url', null)->where('editor_id', $user->id)->orderBy('expires_at', 'ASC')->paginate(($current_page + 1) * 5);
                            if (5 * $current_page < $posts->count())
                                $from = $posts->count() - 5;
                            else
                                $from = ($current_page - 1) * 5;

                            $count_posts = $posts->count();
                            $count = (($current_page * 5) - 5) + 1;
                        } else {
                            $posts = Post::where('archived_at', null)->where('status_task', 1)->where('draft_url', null)->where('editor_id', $user->id)->orderBy('expires_at', 'ASC')->paginate(5);
                            $from = 0;
                            $count_posts = $posts->count();
                            $count = 1;
                        }


                        $text = "<b>В работе: </b> \n";
                        for ($from; $from < $count_posts; $from++) {
                            $deadline_post = $posts[$from]->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';
                            $detail_post = new MessageEntity([
                                'type' => 'bot_command',
                                'offset' => 8,
                                'length' => 10,
                            ]);

                            $detail_post['url'] = '/post' . $posts[$from]->id;
                            $url = $detail_post->get('url');
                            $text .=
                                "Задача № " . $count . ' (' . $posts[$from]->id . ") \n"
                                . "<b>Название задачи: </b> " . $posts[$from]->title . " \n"
                                . "<b>Дедлайн: </b> " . $deadline_post . "\n"
                                . "<b>Подробнее: </b>" . $url . "\n"
                                . " \n";
                            $count++;

                        }

                        if (empty($text)) {
                            $text = 'Пусто';
                        }

                        $count_posts = Post::where('archived_at', null)->where('status_task', 1)->where('editor_id', $user->id)->where('draft_url', null)->count();
                        $status_task = ($status_task) ? $status_task : 2;
                        $pagination = $this->TasksPagination($current_page, $count_posts, $status_task);
                        $this->telegram->sendMessage([
                            'chat_id' => $user_id,
                            'parse_mode' => 'HTML',
                            'text' => $text,
                            'reply_markup' => json_encode([
                                'inline_keyboard' => [
                                    $pagination['keyboard'],
                                ],
                            ]),
                        ]);
                        break;

                    case $status[3]:
                        $from = 0;
                        if ($current_page && $current_page != 1) {
                            $posts = Post::where('archived_at', null)->where('draft_url', '!=', null)->whereNull('publication_url')->where('approved', null)->where('on_moderate', 0)->where('editor_id', $user->id)->orderBy('expires_at', 'ASC')->paginate(($current_page + 1) * 5);
                            if (5 * $current_page < $posts->count())
                                $from = $posts->count() - 5;
                            else
                                $from = ($current_page - 1) * 5;

                            $count_posts = $posts->count();
                            $count = (($current_page * 5) - 5) + 1;

                        } else {

                            $posts = Post::where('archived_at', null)->where('draft_url', '!=', null)->whereNull('publication_url')->where('approved', null)->where('on_moderate', 0)->where('editor_id', $user->id)->orderBy('expires_at', 'ASC')->paginate(5);
                            $from = 0;
                            $count_posts = $posts->count();
                            $count = 1;
                        }


                        $text = "<b>Нужна проверка: </b> \n";
                        for ($from; $from < $count_posts; $from++) {
                            $deadline_post = $posts[$from]->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';
                            $detail_post = new MessageEntity([
                                'type' => 'bot_command',
                                'offset' => 8,
                                'length' => 10,
                            ]);
                            $detail_post['url'] = '/post' . $posts[$from]->id;
                            $url = $detail_post->get('url');
                            $text .=
                                "Задача № " . $count . ' (' . $posts[$from]->id . ") \n"
                                . "<b>Название задачи: </b> " . $posts[$from]->title . " \n"
                                . "<b>Дедлайн: </b> " . $deadline_post . "\n"
                                . "<b>Подробнее: </b>" . $url . "\n"
                                . " \n";

                            $count++;
                        }

                        if (empty($text)) {
                            $text = 'Пусто';
                        }

                        $count_posts = Post::where('archived_at', null)->where('draft_url', '!=', null)->whereNull('publication_url')->where('approved', null)->where('editor_id', $user->id)->where('on_moderate', 0)->count();
                        $status_task = ($status_task) ? $status_task : 3;
                        $pagination = $this->TasksPagination($current_page, $count_posts, $status_task);
                        $this->telegram->sendMessage([
                            'chat_id' => $user_id,
                            'parse_mode' => 'HTML',
                            'text' => $text,
                            'reply_markup' => json_encode([
                                'inline_keyboard' => [
                                    $pagination['keyboard'],
                                ],
                            ]),
                        ]);

                        break;
                    case $status[4]:

                        $from = 0;
                        if ($current_page && $current_page != 1) {
                            $posts = Post::where('archived_at', null)->where('approved', true)->whereNull('publication_url')->where('editor_id', $user->id)->orderBy('expires_at', 'ASC')->paginate(($current_page + 1) * 5);
                            if (5 * $current_page < $posts->count())
                                $from = $posts->count() - 5;
                            else
                                $from = ($current_page - 1) * 5;

                            $count_posts = $posts->count();
                            $count = (($current_page * 5) - 5) + 1;

                        } else {
                            $posts = Post::where('archived_at', null)->where('approved', true)->whereNull('publication_url')->where('editor_id', $user->id)->orderBy('expires_at', 'ASC')->paginate(5);
                            $from = 0;
                            $count_posts = $posts->count();
                            $count = 1;
                        }


                        $text = "<b>Ждет публикации: </b> \n";
                        for ($from; $from < $count_posts; $from++) {
                            $deadline_post = $posts[$from]->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';

                            $detail_post = new MessageEntity([
                                'type' => 'bot_command',
                                'offset' => 8,
                                'length' => 10,
                            ]);
                            $detail_post['url'] = '/post' . $posts[$from]->id;
                            $url = $detail_post->get('url');
                            $text .=
                                "Задача № " . $count . ' (' . $posts[$from]->id . ") \n"
                                . "<b>Название задачи: </b> " . $posts[$from]->title . " \n"
                                . "<b>Дедлайн: </b> " . $deadline_post . "\n"
                                . "<b>Подробнее: </b>" . $url . "\n"
                                . " \n";
                            $count++;
                        }
                        if (empty($text)) {
                            $text = 'Пусто';
                        }

                        $count_posts = Post::where('archived_at', null)->where('approved', true)->where('editor_id', $user->id)->whereNull('publication_url')->count();
                        $status_task = ($status_task) ? $status_task : 4;
                        $pagination = $this->TasksPagination($current_page, $posts->count(), $status_task);
                        $this->telegram->sendMessage([
                            'chat_id' => $user_id,
                            'parse_mode' => 'HTML',
                            'text' => $text,
                            'reply_markup' => json_encode([
                                'inline_keyboard' => [
                                    $pagination['keyboard'],
                                ],
                            ]),
                        ]);

                        break;


                    case 'take_tasks':
                        $this->telegram->sendMessage([
                            'chat_id' => $user_id,
                            'text' => 'Выберите статус задач, которые необходимо отобразить. ',
                            'reply_markup' => $keyboard,
                        ]);
                        return 0;
                        break;


                    case $status[5]:

                        $from = 0;
                        if ($current_page && $current_page != 1) {
                            $posts = Post::whereNull('archived_at')->where('publication_url', '!=', null)->where('posting', true)->where('smm_id', null)->where('editor_id', $user->id)->orderBy('expires_at', 'ASC')->paginate(($current_page + 1) * 5);
                            if (5 * $current_page < $posts->count())
                                $from = $posts->count() - 5;
                            else
                                $from = ($current_page - 1) * 5;

                            $count_posts = $posts->count();
                            $count = (($current_page * 5) - 5) + 1;

                        } else {
                            $posts = Post::whereNull('archived_at')->where('publication_url', '!=', null)->where('posting', true)->where('smm_id', null)->where('editor_id', $user->id)->orderBy('expires_at', 'ASC')->paginate(5);
                            $from = 0;
                            $count_posts = $posts->count();
                            $count = 1;
                        }


                        $text = "<b>Ожидают размещения в соц-сетях: </b> \n";
                        for ($from; $from < $count_posts; $from++) {
                            $deadline_post = $posts[$from]->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';


                            $detail_post = new MessageEntity([
                                'type' => 'bot_command',
                                'offset' => 8,
                                'length' => 10,
                            ]);
                            $detail_post['url'] = '/post' . $posts[$from]->id;
                            $url = $detail_post->get('url');
                            $text .=
                                "Задача № " . $count . ' (' . $posts[$from]->id . ") \n"
                                . "<b>Название задачи: </b> " . $posts[$from]->title . " \n"
                                . "<b>Дедлайн: </b> " . $deadline_post . "\n"
                                . "<b>Подробнее: </b>" . $url . "\n"
                                . " \n";

                            $count++;
                        }

                        if (empty($text)) {
                            $text = 'Пусто';
                        }

                        $count_posts = Post::whereNull('archived_at')->where('publication_url', '!=', null)->where('posting', true)->where('editor_id', $user->id)->where('smm_id', null)->count();
                        $status_task = ($status_task) ? $status_task : 5;
                        $pagination = $this->TasksPagination($current_page, $count_posts, $status_task);
                        $this->telegram->sendMessage([
                            'chat_id' => $user_id,
                            'parse_mode' => 'HTML',
                            'text' => $text,
                            'reply_markup' => json_encode([
                                'inline_keyboard' => [
                                    $pagination['keyboard'],
                                ],
                            ]),
                        ]);

                        break;
                    case $status[6]:
                        $from = 0;
                        if ($current_page && $current_page != 1) {
                            $posts = Post::where('archived_at', null)->where('posting', 1)->where('smm_id', '!=', null)->where('targeting', 1)->where('editor_id', $user->id)->where('target_id', null)->orderBy('expires_at', 'ASC')->paginate(($current_page + 1) * 5);
                            if (5 * $current_page < $posts->count())
                                $from = $posts->count() - 5;
                            else
                                $from = ($current_page - 1) * 5;

                            $count_posts = $posts->count();
                            $count = (($current_page * 5) - 5) + 1;

                        } else {
                            $posts = Post::where('archived_at', null)->where('posting', 1)->where('smm_id', '!=', null)->where('targeting', 1)->where('editor_id', $user->id)->where('target_id', null)->orderBy('expires_at', 'ASC')->paginate(5);
                            $from = 0;
                            $count_posts = $posts->count();
                            $count = 1;
                        }


                        $text = "<b>Ожидают таргетированную рекламу: </b> \n";
                        for ($from; $from < $count_posts; $from++) {
                            $deadline_post = $posts[$from]->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';
                            $detail_post = new MessageEntity([
                                'type' => 'bot_command',
                                'offset' => 8,
                                'length' => 10,
                            ]);
                            $detail_post['url'] = '/post' . $posts[$from]->id;
                            $url = $detail_post->get('url');
                            $text .=
                                "Задача № " . $count . ' (' . $posts[$from]->id . ") \n"
                                . "<b>Название задачи: </b> " . $posts[$from]->title . " \n"
                                . "<b>Дедлайн: </b> " . $deadline_post . "\n"
                                . "<b>Подробнее: </b>" . $url . "\n"
                                . " \n";

                            $count++;
                        }

                        if (empty($text)) {
                            $text = 'Пусто';
                        }

                        $count_posts = Post::where('archived_at', null)->where('posting', 1)->where('smm_id', '!=', null)->where('targeting', 1)->where('editor_id', $user->id)->where('target_id', null)->count();
                        $status_task = ($status_task) ? $status_task : 6;
                        $pagination = $this->TasksPagination($current_page, $count_posts, $status_task);
                        $this->telegram->sendMessage([
                            'chat_id' => $user_id,
                            'parse_mode' => 'HTML',
                            'text' => $text,
                            'reply_markup' => json_encode([
                                'inline_keyboard' => [
                                    $pagination['keyboard'],
                                ],
                            ]),
                        ]);


                        break;
                    case $status[7]:
                        $from = 0;
                        if ($current_page && $current_page != 1) {
                            $posts = Post::where('archived_at', null)->where('posting', 1)->where('smm_id', '!=', null)->where('seeding', 1)->where('editor_id', $user->id)->where('seeder_id', null)->orderBy('expires_at', 'ASC')->paginate(($current_page + 1) * 5);
                            if (5 * $current_page < $posts->count())
                                $from = $posts->count() - 5;
                            else
                                $from = ($current_page - 1) * 5;

                            $count_posts = $posts->count();
                            $count = (($current_page * 5) - 5) + 1;

                        } else {
                            $posts = Post::where('archived_at', null)->where('posting', 1)->where('smm_id', '!=', null)->where('seeding', 1)->where('editor_id', $user->id)->where('seeder_id', null)->orderBy('expires_at', 'ASC')->paginate(5);
                            $from = 0;
                            $count_posts = $posts->count();
                            $count = 1;
                        }

                        $text = "<b>Ожидают посева: </b> \n";
                        for ($from; $from < $count_posts; $from++) {
                            $deadline_post = $posts[$from]->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';
                            $detail_post = new MessageEntity([
                                'type' => 'bot_command',
                                'offset' => 8,
                                'length' => 10,
                            ]);
                            $detail_post['url'] = '/post' . $posts[$from]->id;
                            $url = $detail_post->get('url');
                            $text .=
                                "Задача № " . $count . ' (' . $posts[$from]->id . ") \n"
                                . "<b>Название задачи: </b> " . $posts[$from]->title . " \n"
                                . "<b>Дедлайн: </b> " . $deadline_post . "\n"
                                . "<b>Подробнее: </b>" . $url . "\n"
                                . " \n";

                            $count++;

                        }
                        if (empty($text)) {
                            $text = 'Пусто';
                        }

                        $count_posts = Post::where('archived_at', null)->where('posting', 1)->where('smm_id', '!=', null)->where('seeding', 1)->where('editor_id', $user->id)->where('seeder_id', null)->count();
                        $status_task = ($status_task) ? $status_task : 7;
                        $pagination = $this->TasksPagination($current_page, $count_posts, $status_task);
                        $this->telegram->sendMessage([
                            'chat_id' => $user_id,
                            'parse_mode' => 'HTML',
                            'text' => $text,
                            'reply_markup' => json_encode([
                                'inline_keyboard' => [
                                    $pagination['keyboard'],
                                ],
                            ]),
                        ]);

                        break;
                    case $status[8]:
                        $from = 0;
                        if ($current_page && $current_page != 1) {
                            $posts = Post::where('archived_at', null)->where('posting', 1)->where('smm_id', '!=', null)->where('commenting', 1)->where('editor_id', $user->id)->where('commentator_id', null)->orderBy('expires_at', 'ASC')->paginate(($current_page + 1) * 5);
                            if (5 * $current_page < $posts->count())
                                $from = $posts->count() - 5;
                            else
                                $from = ($current_page - 1) * 5;

                            $count_posts = $posts->count();
                            $count = (($current_page * 5) - 5) + 1;
                        } else {
                            $posts = Post::where('archived_at', null)->where('posting', 1)->where('smm_id', '!=', null)->where('commenting', 1)->where('editor_id', $user->id)->where('commentator_id', null)->orderBy('expires_at', 'ASC')->paginate(5);
                            $from = 0;
                            $count_posts = $posts->count();
                            $count = 1;
                        }


                        $text = "<b>Ожидают комментирования: </b> \n";
                        for ($from; $from < $count_posts; $from++) {
                            $deadline_post = $posts[$from]->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';
                            $detail_post = new MessageEntity([
                                'type' => 'bot_command',
                                'offset' => 8,
                                'length' => 10,
                            ]);
                            $detail_post['url'] = '/post' . $posts[$from]->id;
                            $url = $detail_post->get('url');
                            $text .=
                                "Задача № " . $count . ' (' . $posts[$from]->id . ") \n"
                                . "<b>Название задачи: </b> " . $posts[$from]->title . " \n"
                                . "<b>Дедлайн: </b> " . $deadline_post . "\n"
                                . "<b>Подробнее: </b>" . $url . "\n"
                                . " \n";

                            $count++;

                        }

                        if (empty($text)) {
                            $text = 'Пусто';
                        }

                        $count_posts = Post::where('archived_at', null)->where('posting', 1)->where('smm_id', '!=', null)->where('commenting', 1)->where('editor_id', $user->id)->where('commentator_id', null)->count();
                        $status_task = ($status_task) ? $status_task : 8;
                        $pagination = $this->TasksPagination($current_page, $count_posts, $status_task);
                        $this->telegram->sendMessage([
                            'chat_id' => $user_id,
                            'parse_mode' => 'HTML',
                            'text' => $text,
                            'reply_markup' => json_encode([
                                'inline_keyboard' => [
                                    $pagination['keyboard'],
                                ],
                            ]),
                        ]);
                        break;
                }


            }
        }


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

    private function TasksPagination($current_page, $count_posts, $status_task)
    {
        $items = range(0, $count_posts); // required.
        $command = 'takePosts'; // optional. Default: pagination
        $selected_page = ($current_page) ? $current_page : 1;            // optional. Default: 1
        $labels = [              // optional. Change button labels (showing defaults)
            'default' => '%d',
            'first' => '« %d',
            'previous' => '‹ %d',
            'current' => '· %d ·',
            'next' => '%d ›',
            'last' => '%d »',
        ];

// optional. Change the callback_data format, adding placeholders for data (showing default)
        $callback_data_format = 'command={COMMAND}&status_task=' . $status_task . '&oldPage={OLD_PAGE}&newPage={NEW_PAGE}';


        // Define inline keyboard pagination.
        $ikp = new InlineKeyboardPagination($items, $command);
        $ikp->setMaxButtons(5, false); // Second parameter set to always show 7 buttons if possible.
        $ikp->setLabels($labels);
        $ikp->setCallbackDataFormat($callback_data_format);

// Get pagination.
        $pagination = $ikp->getPagination($selected_page);

// or, in 2 steps.
        $ikp->setSelectedPage($selected_page);


        return $pagination;
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
                    'text' => 'Ваша идея отправлена.'
                ]);

                $ideaController = new IdeaController();
                $ideaController->notifyEditors($idea, $user->id);

                break;

        }
        return 'ok';
    }

}
