<?php


namespace App\Http\Controllers\Telegram\Traits\Editor;


use App\Http\Controllers\TelegramBotController;
use App\Models\Post;
use App\Models\Project;
use App\Models\SocialNetwork;
use App\Models\Temp_task;
use App\Models\User;
use Telegram\Bot\Keyboard\Keyboard;
use Illuminate\Support\Str;
use Telegram\Bot\Api as Api;

trait CreateTask
{

    /**
     * Создание задачи редактором
     * @param $telegram_user
     * @param $user
     * @param $message
     * @return array
     */
    public function create($telegram_user, $user, $message) {
        // шаги пользователя
        $steps = ['create_task', 'add_description', 'give_projects', 'give_journalist', 'give_deadline',
            'need_posting','wait_posting', 'need_targeting', 'wait_targeting', 'need_seeding',
            'wait_seeding', 'wait_comment' ];

        // Определяем действия выбора инлайновых клавиатур, end_create - окончание создание задачи
        $actions = ['yes', 'no', 'end_create'];

        $completeMessage = [];
        $keyboard = Keyboard::make()
            ->inline();

        $temp_task = Temp_task::get()->where('editor_id', $user->id)->first();

        switch($telegram_user->current_step) {
            case $steps[0]:
                $telegram_user->changeStepsUser($steps[1], $telegram_user->forward_step);
                $temp_task = new Temp_task([
                    'telegram_id' => $telegram_user->telegram_id,
                    'editor_id' => $user->id,
                ]);
                $temp_task->save();
                $completeMessage['text'] = 'Напишите название задачи на 1 строке и тезисы на 2 строке';
                break;
            case $steps[1]:
                if(!isset($message['message']['text'])) {
                    $completeMessage['text'] = 'Напишите тезисы на второй строке!';
                    break;
                }

                $text = trim($message['message']['text']);
                $regular = "/[\n,]+/";
                $message = preg_split($regular, $text);
                $header = $message[0];
                unset($message[0]);
                $text = implode(" ", $message);

                if(empty($text)) {
                    $completeMessage['text'] = 'Напишите тезисы на второй строке!';
                    break;
                }

                $telegram_user->changeStepsUser($steps[2], $telegram_user->forward_step);

                if($user->projects->count() > 0) {
                    foreach($user->projects as $project) {
                        $keyboard->row(
                            Keyboard::inlineButton(['text' => $project->name , 'callback_data' => 'project_'.$project->id]));
                    }
                } else {
                    $completeMessage['text'] = 'У вас нет проектов в системе. Задача удалена.';
                    $temp_task->delete();
                    $telegram_user->changeStepsUser(null, null);
                    break;
                }

                $completeMessage['keyboard'] = $keyboard;
                $completeMessage['text'] = 'Выберите проект';
                $temp_task->title = $header;
                $temp_task->text = $text;
                $temp_task->save();
                break;
            case $steps[2]:
                if(isset($message['message']['text'])) {
                    $completeMessage['text'] = 'Выберите проект!';
                    break;
                }
                $telegram_user->changeStepsUser($steps[3], $telegram_user->forward_step);
                $take_id_project = $message->getCallbackQuery()->getData();
                $take_id_project = str_replace('project_', "", $take_id_project);

                $temp_task->project_id = $take_id_project;
                $temp_task->save();
                $projects = Project::all()->where('id', $take_id_project)->first();

                foreach($projects->users as $user) {
                    $keyboard->row(
                        Keyboard::inlineButton(['text' => $user->name , 'callback_data' => 'journalist_'.$user->id]));
                }
                $keyboard->row(
                    Keyboard::inlineButton(['text' => 'Без назначения' , 'callback_data' => 'journalist_null']));
                $completeMessage['text'] = 'Выберите журналиста';
                $completeMessage['keyboard'] = $keyboard;
                break;
            case $steps[3]:
                if(isset($message['message']['text'])) {
                    $completeMessage['text'] = 'Выберите журналиста!';
                    break;
                }
                $telegram_user->changeStepsUser($steps[4], $telegram_user->forward_step);
                $take_id_jorunalist = $message->getCallbackQuery()->getData();
                $take_id_jorunalist = str_replace('journalist_', "", $take_id_jorunalist);

                if($take_id_jorunalist != "null") {
                    $temp_task->journalist_id = $take_id_jorunalist;
                    $temp_task->save();
                }
                $completeMessage['text'] = 'Чтобы задать срок задачи, отправьте в ответном сообщении количество часов до дедлайна';
                break;
            case $steps[4]:
                if($message->isType('callback_query')) {
                    $completeMessage['text'] = 'Введите числовое значение времени!';
                    break;
                } elseif(!ctype_digit($message['message']['text'])) {
                    $completeMessage['text'] = 'Введите числовое значение времени!';
                    break;
                }
                $telegram_user->changeStepsUser($steps[5], $telegram_user->forward_step);

                $current_time = $temp_task->created_at;
                $current_time = strtotime($current_time);
                $current_time = strtotime("+".$message['message']['text']." hours");
                $temp_task->expires_at = $current_time;
                $temp_task->save();

                $completeMessage['keyboard'] = $this->getKeyboardActions($keyboard);
                $completeMessage['text'] = 'Размещаем в соцсетях?';
                break;
            case $steps[5]:
                if(isset($message['message']['text'])) {
                    $completeMessage['text'] = 'Ответьте на вопрос!';
                    break;
                }


                $take_action = '';
                foreach($actions as $key => $value) {
                    $status_action = strpos($message->getCallbackQuery()->getData(),$value);
                    if($status_action !== false) {
                        $take_action = $value;
                    }
                }

                if(empty($take_action)) {
                    $completeMessage['text'] = 'Ответьте на вопрос!';
                    break;
                }

                switch($take_action) {
                    case $actions[0]:

                        $telegram_user->changeStepsUser($steps[6], $telegram_user->forward_step);
                        $text = "Введите цифрами социальные сети: \n"
                            ."1 - Вконтакте \n"
                            ."2 - Одноклассники \n"
                            ."3 - Facebook \n"
                            ."4 - Instagram \n"
                            ."5 - Я.Дзен \n"
                            ."6 - Я.Район \n"
                            ."7 - Youtube \n"
                            ."8 - Telegram \n"
                            ."0 - Выбрать соц сети, которые уже привязаны к проекту \n"
                            ."Пример: 123 Разместить в данных соц-сетях \n"
                            ."Напишите комментарий на второй строке \n";
                        $completeMessage['text'] = $text;
                        return $completeMessage;
                        break;
                    case $actions[1]:
                        $telegram_user->changeStepsUser($steps[7], $telegram_user->forward_step);
                        $completeMessage['keyboard'] = $this->getKeyboardActions($keyboard);
                        $completeMessage['text'] = "Делаем таргетированную рекламу?";
                        return $completeMessage;
                        break;
                    case $actions[2]:
                        $post = new Post();
                        $post->fill($temp_task->toArray());
                        if($post->journalist_id != null) {
                            $post->status_id = 1;
                        } else {
                            $post->status_id = 2;
                        }
                        $post->save();
                        $telegram_user->changeStepsUser(null, $telegram_user->current_step);
                        $telegram_user->post_id = null;
                        $telegram_user->save();

                        $info_new_task = '';
                        $deadline_post = $post->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';
                        $formattText = $this->formatPostText($temp_task->text);

                        (!is_null($temp_task->journalist_id)) ? $user_journalist = User::find($temp_task->journalist_id)->name :  $user_journalist = 'Без назначения';
                        $info_new_task .= 'Задача <b>'. $temp_task->title.'</b> создана!' . "\n"
                            . '<b>Тезисы:  </b>' . "\n"
                            . $formattText . "\n"
                            . "Дедлайн: " . $deadline_post . "\n"
                            . '<b>Исполнитель: </b>' .  $user_journalist . "\n";

                        if($temp_task->posting) {
                            $info_new_task  .= '<b>Соц-сети:</b> ';
                            ($temp_task->posting_to_vk) ? $info_new_task .= 'ВК, ' : '';
                            ($temp_task->posting_to_ok) ? $info_new_task .= 'OK, ' : '';
                            ($temp_task->posting_to_fb) ? $info_new_task .= 'FB, ' : '';
                            ($temp_task->posting_to_ig) ? $info_new_task .= 'IG, ' : '';
                            ($temp_task->posting_to_y_dzen) ? $info_new_task .= 'Я.Дзен, ' : '';
                            ($temp_task->posting_to_y_street) ? $info_new_task .= 'Я.Районы, ' : '';
                            ($temp_task->posting_to_yt) ? $info_new_task .= 'YT, ' : '';
                            ($temp_task->posting_to_tt) ? $info_new_task .= 'TT, ' : '';
                            ($temp_task->posting_to_tg) ? $info_new_task .= 'TG, ' : '';

                            $info_new_task = Str::replaceLast(', ','',$info_new_task);
                            $info_new_task .= "\n";


                            if($temp_task->targeting) {
                                $info_new_task  .= "<b>Тарегтированная реклама:</b> Да \n";

                            }

                            if($temp_task->seeding) {
                                $info_new_task  .= "<b>Посевы:</b> Да \n";
                            }

                            if($temp_task->commenting) {
                                $info_new_task  .= "<b>Комментирование:</b> Да  \n";

                            }

                        }
                        $info_new_task  .= "<b>Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a></b> \n";
                        $temp_task->delete();
                        $completeMessage['text'] = $info_new_task;
                        return $completeMessage;
                        break;
                }
                $telegram_user->changeStepsUser($steps[6], $telegram_user->forward_step);
                break;
            case $steps[6]:
                if($message->isType('callback_query')) {
                    $completeMessage['text'] = 'Выберите соц-сети';
                    break;
                }


                $temp_task->posting = 1;

                $text = trim($message['message']['text']);
                $regular = "/[\s,]+/";
                $message = preg_split($regular, $text);
                $take_social_posting = $message[0];
                unset($message[0]);
                $comment = implode(" ",$message);


                $text_to_user = "Вы выбрали соц-сети: ";
                for($i = 0; $i < Str::length($take_social_posting); $i++) {
                    switch($take_social_posting[$i]) {
                        case 0:
                            $count = 0;
                            $project = Project::all()->where('id',$temp_task->project_id)->first();
                            if($project->vk) { $temp_task->posting_to_vk = 1; $text_to_user .= 'вконтакте,';   $count++;}
                            if($project->ok) { $temp_task->posting_to_ok = 1; $text_to_user .= 'одноклассники,'; $count++;}
                            if($project->fb) { $temp_task->posting_to_fb = 1; $text_to_user .= 'facebook,'; $count++;}
                            if($project->insta) { $temp_task->posting_to_ig = 1; $text_to_user .= 'instagram,'; $count++;}
                            if($project->y_dzen) { $temp_task->posting_to_y_dzen = 1; $text_to_user .= 'я.дзен,'; $count++;}
                            if($project->y_street) { $temp_task->posting_to_y_street = 1; $text_to_user .= 'я.улица,'; $count++;}
                            if($project->yt) { $temp_task->posting_to_yt = 1; $text_to_user .= 'youtube,'; $count++;}
                            if($project->tt) { $temp_task->posting_to_tt = 1; $text_to_user .= 'tikTok,'; $count++;}
                            if($project->tg) { $temp_task->posting_to_tg = 1; $text_to_user .= 'telegram,'; $count++;}

                            if($count == 0) {
                                $completeMessage['text'] = 'В проекте нет привязанных соц сетей, поэтому выберите из списка.';
                                return $completeMessage;
                            }

                            $text_to_user = Str::replaceLast(',','',$text_to_user);
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
                            $completeMessage['text'] = $text_to_user;
                            break;
                    }
                }

                $telegram_user->changeStepsUser($steps[7], $telegram_user->current_step);

                $temp_task->posting_text = $comment;
                $temp_task->save();
                $text_to_user = Str::replaceLast(',','',$text_to_user);
                $text_to_user .= "\n"
                    ."<b>Делаем таргетированную рекламу?</b>";

                $completeMessage['keyboard'] = $this->getKeyboardActions($keyboard);
                $completeMessage['text'] = $text_to_user;
                break;
            case $steps[7]:
                if(isset($message['message']['text'])) {
                    $completeMessage['text'] = 'Ответьте на вопрос!';
                    break;
                }

                $take_action = '';
                foreach($actions as $key => $value) {
                    $status_action = strpos($message->getCallbackQuery()->getData(),$value);
                    if($status_action !== false) {
                        $take_action = $value;
                    }
                }

                if(empty($take_action)) {
                    $completeMessage['text'] = 'Ответьте на вопрос!';
                    break;
                }

                switch($take_action) {
                    case $actions[0]:

                        $telegram_user->changeStepsUser($steps[8], $telegram_user->forward_step);
                        $text = "Введите цифрами социальные сети: \n"
                            ."1 - Вконтакте \n"
                            ."2 - Одноклассники \n"
                            ."3 - Facebook \n"
                            ."4 - Instagram \n"
                            ."5 - Я.Дзен \n"
                            ."6 - Я.Район \n"
                            ."7 - Youtube \n"
                            ."8 - Telegram \n"
                            ."0 - Выбрать соц сети, которые уже привязаны к проекту \n"
                            ."Пример: 123 Разместить в данных соц-сетях \n"
                            ."Напишите комментарий на второй строке \n";
                        $completeMessage['text'] = $text;
                        return $completeMessage;
                        break;
                    case $actions[1]:
                        $telegram_user->changeStepsUser($steps[9], $telegram_user->forward_step);
                        $completeMessage['keyboard'] = $this->getKeyboardActions($keyboard);
                        $completeMessage['text'] = "Нужен посев?";
                        return $completeMessage;
                        break;
                    case $actions[2]:
                        $post = new Post();
                        $post->fill($temp_task->toArray());

                        if($post->journalist_id != null) {
                            $post->status_id = 1;
                        } else {
                            $post->status_id = 2;
                        }

                        $post->save();
                        $telegram_user->changeStepsUser(null, $telegram_user->current_step);
                        $telegram_user->post_id = null;
                        $telegram_user->save();

                        $info_new_task = '';
                        $deadline_post = $post->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';
                        $formattText = $this->formatPostText($temp_task->text);

                        (!is_null($temp_task->journalist_id)) ? $user_journalist = User::find($temp_task->journalist_id)->name :  $user_journalist = 'Без назначения';
                        $info_new_task .= 'Задача <b>'. $temp_task->title.'</b> создана!' . "\n"
                            . '<b>Тезисы:  </b>' . "\n"
                            . $formattText . "\n"
                            . "Дедлайн: " . $deadline_post . "\n"
                            . '<b>Исполнитель: </b>' .  $user_journalist . "\n";

                        if($temp_task->posting) {
                            $info_new_task  .= '<b>Соц-сети:</b> ';
                            ($temp_task->posting_to_vk) ? $info_new_task .= 'ВК, ' : '';
                            ($temp_task->posting_to_ok) ? $info_new_task .= 'OK, ' : '';
                            ($temp_task->posting_to_fb) ? $info_new_task .= 'FB, ' : '';
                            ($temp_task->posting_to_ig) ? $info_new_task .= 'IG, ' : '';
                            ($temp_task->posting_to_y_dzen) ? $info_new_task .= 'Я.Дзен, ' : '';
                            ($temp_task->posting_to_y_street) ? $info_new_task .= 'Я.Районы, ' : '';
                            ($temp_task->posting_to_yt) ? $info_new_task .= 'YT, ' : '';
                            ($temp_task->posting_to_tt) ? $info_new_task .= 'TT, ' : '';
                            ($temp_task->posting_to_tg) ? $info_new_task .= 'TG, ' : '';

                            $info_new_task = Str::replaceLast(', ','',$info_new_task);
                            $info_new_task .= "\n";


                            if($temp_task->targeting) {
                                $info_new_task  .= "<b>Тарегтированная реклама:</b> Да \n";

                            }

                            if($temp_task->seeding) {
                                $info_new_task  .= "<b>Посевы:</b> Да \n";
                            }

                            if($temp_task->commenting) {
                                $info_new_task  .= "<b>Комментирование:</b> Да  \n";

                            }

                        }

                        $info_new_task  .= "<b>Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a></b> \n";
                        $temp_task->delete();
                        $completeMessage['text'] = $info_new_task;
                        return $completeMessage;
                        break;
                }

                break;
            case $steps[8]:
                if($message->isType('callback_query')) {
                    $completeMessage['text'] = 'Выберите соц-сети';
                    break;
                }


                $temp_task->targeting = 1;
                $regular = "/[\s,]+/";
                $text = trim($message['message']['text']);
                $message = preg_split($regular, $text);
                $take_social_target = $message[0];
                unset($message[0]);
                $comment = implode(" ",$message);

                $text_to_user = "Вы выбрали соц-сети: ";

                for($i = 0; $i < Str::length($take_social_target); $i++) {
                    switch($take_social_target[$i]) {
                        case 0:
                            $count = 0;
                            $project = Project::all()->where('id',$temp_task->project_id)->first();
                            if($project->vk) { $temp_task->targeting_to_vk = 1; $text_to_user .= 'вконтакте,'; }
                            if($project->ok) { $temp_task->targeting_to_ok = 1; $text_to_user .= 'одноклассники,'; }
                            if($project->fb) { $temp_task->targeting_to_fb = 1; $text_to_user .= 'facebook,'; }
                            if($project->insta) { $temp_task->targeting_to_ig = 1; $text_to_user .= 'instagram,'; }
                            if($project->y_dzen) { $temp_task->targeting_to_y_dzen = 1; $text_to_user .= 'я.дзен,'; }
                            if($project->y_street) { $temp_task->targeting_to_y_street = 1; $text_to_user .= 'я.улица,'; }
                            if($project->yt) { $temp_task->targeting_to_yt = 1; $text_to_user .= 'youtube,'; }
                            if($project->tg) { $temp_task->targeting_to_tg = 1; $text_to_user .= 'telegram,'; }

                            if($count == 0) {
                                $completeMessage['text'] = 'В проекте нет привязанных соц сетей, поэтому выберите из списка.';
                                return $completeMessage;
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
                            $temp_task->posting = 0;
                            $temp_task->save();
                            $completeMessage['text'] = $text_to_user;
                            break;
                    }
                }

                $temp_task->posting_text = $comment;
                $temp_task->save();
                $text_to_user = Str::replaceLast(',','',$text_to_user);
                $text_to_user .= "\n"
                    ."<b>Нужен посев?</b>";

                $completeMessage['keyboard'] = $this->getKeyboardActions($keyboard);
                $completeMessage['text'] = $text_to_user;
                $telegram_user->changeStepsUser($steps[9], $telegram_user->current_step);
                break;
            case $steps[9]:
                if(isset($message['message']['text'])) {
                    $completeMessage['text'] = 'Ответьте на вопрос!';
                    break;
                }

                $take_action = '';
                foreach($actions as $key => $value) {
                    $status_action = strpos($message->getCallbackQuery()->getData(),$value);
                    if($status_action !== false) {
                        $take_action = $value;
                    }
                }

                if(empty($take_action)) {
                    $completeMessage['text'] = 'Ответьте на вопрос!';
                    break;
                }

                switch($take_action) {
                    case $actions[0]:
                        $telegram_user->changeStepsUser($steps[10], $telegram_user->forward_step);
                        $text = "Введите цифрами социальные сети: \n"
                            ."1 - Вконтакте \n"
                            ."2 - Одноклассники \n"
                            ."3 - Facebook \n"
                            ."4 - Instagram \n"
                            ."5 - Я.Дзен \n"
                            ."6 - Я.Район \n"
                            ."7 - Youtube \n"
                            ."8 - Telegram \n"
                            ."0 - Выбрать соц сети, которые уже привязаны к проекту \n"
                            ."Пример: 123 Разместить в данных соц-сетях \n"
                            ."Напишите комментарий на второй строке \n";
                        $completeMessage['text'] = $text;
                        return $completeMessage;
                        break;
                    case $actions[1]:
                        if($temp_task->posting || $temp_task->seeding) {
                            $completeMessage['text'] =  'Нужно комментирование? 1 - Да 2 - Нет. На второй строчке напишите комментарий';
                           $telegram_user->changeStepsUser($steps[11], $telegram_user->current_step);
                        } else {
                            $post = new Post();
                            $post->fill($temp_task->toArray());
                            $post->save();
                            $telegram_user->changeStepsUser(null, $telegram_user->current_step);
                            $telegram_user->post_id = null;
                            $telegram_user->save();
                            $temp_task->delete();
                            $completeMessage['text'] = 'Задача <b>'. $temp_task->title.'</b> создана!';
                            (new TelegramBotController(new Api()))->storeMessage($post);
                        }
                        return $completeMessage;
                        break;
                    case $actions[2]:
                        $post = new Post();
                        $post->fill($temp_task->toArray());

                        if($post->journalist_id != null) {
                            $post->status_id = 1;
                        } else {
                            $post->status_id = 2;
                        }
                        $post->save();
                        $info_new_task = '';
                        $formattText = $this->formatPostText($temp_task->text);
                        $deadline_post = $post->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';
                        (!is_null($temp_task->journalist_id)) ? $user_journalist = User::find($temp_task->journalist_id)->name :  $user_journalist = 'Без назначения';
                        $info_new_task .= 'Задача <b>'. $temp_task->title.'</b> создана!' . "\n"
                            . '<b>Тезисы:  </b>' . "\n"
                            . $formattText . "\n"
                            . "Дедлайн: " . $deadline_post . "\n"
                            . '<b>Исполнитель: </b>' .  $user_journalist . "\n";

                        if($temp_task->posting) {
                            $info_new_task  .= '<b>Соц-сети:</b> ';
                            ($temp_task->posting_to_vk) ? $info_new_task .= 'ВК, ' : '';
                            ($temp_task->posting_to_ok) ? $info_new_task .= 'OK, ' : '';
                            ($temp_task->posting_to_fb) ? $info_new_task .= 'FB, ' : '';
                            ($temp_task->posting_to_ig) ? $info_new_task .= 'IG, ' : '';
                            ($temp_task->posting_to_y_dzen) ? $info_new_task .= 'Я.Дзен, ' : '';
                            ($temp_task->posting_to_y_street) ? $info_new_task .= 'Я.Районы, ' : '';
                            ($temp_task->posting_to_yt) ? $info_new_task .= 'YT, ' : '';
                            ($temp_task->posting_to_tt) ? $info_new_task .= 'TT, ' : '';
                            ($temp_task->posting_to_tg) ? $info_new_task .= 'TG, ' : '';

                            $info_new_task = Str::replaceLast(', ','',$info_new_task);
                            $info_new_task .= "\n";


                            if($temp_task->targeting) {
                                $info_new_task  .= "<b>Тарегтированная реклама:</b> Да \n";

                            }

                            if($temp_task->seeding) {
                                $info_new_task  .= "<b>Посевы:</b> Да \n";
                            }

                            if($temp_task->commenting) {
                                $info_new_task  .= "<b>Комментирование:</b> Да  \n";

                            }

                        }

                        $info_new_task  .= "<b>Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a></b> \n";

                        $telegram_user->changeStepsUser(null, $telegram_user->current_step);
                        $telegram_user->post_id = null;
                        $telegram_user->save();
                        $completeMessage['text'] = $info_new_task;
                        $temp_task->delete();
                        (new TelegramBotController(new Api()))->storeMessage($post);
                        return $completeMessage;
                        break;
                }

                break;
            case $steps[10]:
                if($message->isType('callback_query')) {
                    $completeMessage['text'] = 'Выберите соц-сети';
                    break;
                }

                $temp_task->seeding = 1;
                $regular = "/[\s,]+/";
                $text = trim($message['message']['text']);
                $message = preg_split($regular, $text);
                $take_social_seeding = $message[0];
                unset($message[0]);
                $comment = implode(" ",$message);


                $text_to_user = "Вы выбрали соц-сети: ";
                for($i = 0; $i < Str::length($take_social_seeding); $i++) {
                    switch($take_social_seeding[$i]) {
                        case 0:
                            $project = Project::all()->where('id',$temp_task->project_id)->first();
                            $temp_task->seeding_to_vk = 1;
                            $temp_task->seeding_to_ok = 1;
                            $temp_task->seeding_to_fb = 1;
                            $temp_task->seeding_to_insta = 1;
                            $temp_task->seeding_to_y_dzen = 1;
                            $temp_task->seeding_to_y_street = 1;
                            $temp_task->seeding_to_yt = 1;
                            $temp_task->seeding_to_tg = 1;
                            $temp_task->save();
                            $text_to_user = Str::replaceLast(',','',$text_to_user);
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
                            $temp_task->posting = 0;
                            $temp_task->save();
                            $completeMessage['text'] = $text_to_user;
                            break;
                    }
                }

                $telegram_user->changeStepsUser($steps[11], $telegram_user->current_step);
                $temp_task->seeding_text = $comment;
                $temp_task->save();

                $text_to_user .= "\n"
                    ."<b>Нужно комментирование? 1 - Да 2 - Нет. На второй строчке напишите комментарий</b>";

                $completeMessage['text'] = $text_to_user;
                break;
            case $steps[11]:
                if($message->isType('callback_query')) {
                    $completeMessage['text'] = 'Введите комментарий';
                    break;
                }

                $regular = "/[\s,]+/";
                $text = trim($message['message']['text']);
                $message = preg_split($regular, $text);
                $take_commenting = $message[0];

                if($take_commenting == 1) {
                    unset($message[0]);
                    $comment = implode(" ",$message);
                    $temp_task->commenting = 1;
                    $temp_task->commenting_text = $comment;
                    $telegram_user->changeStepsUser(null, $telegram_user->current_step);
                    $telegram_user->post_id = null;
                    $telegram_user->save();
                    $temp_task->save();
                }

                $telegram_user->changeStepsUser(null, $telegram_user->current_step);
                $telegram_user->post_id = null;
                $telegram_user->save();

                $post = new Post();
                $post->fill($temp_task->toArray());
                if($post->journalist_id != null) {
                    $post->status_id = 1;
                } else {
                    $post->status_id = 2;
                }

                $deadline_post = $post->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';
                $post->save();


//                Создаем массив выбранных соц. сетей для задачи
                $syncSocialNetworks = [];
                foreach ($temp_task->toArray() as $fieldName => $fieldValue) {
                    if (!\in_array($fieldName, [
                        'targeting_to_vk',
                        'targeting_to_ok',
                        'targeting_to_fb',
                        'targeting_to_ig',
                        'targeting_to_tg',
                        'targeting_to_yt',
                        'targeting_to_y_street',
                        'targeting_to_y_dzen',
                        ], true)) {
                        continue;
                    }

                    if ($fieldValue) {
                        $socialNetworkSlug = mb_substr($fieldName, 13);
                        $socialNetwork = SocialNetwork::where('slug', '=', $socialNetworkSlug)->first()->id ?? null;
                        if ($socialNetwork) {
                            $syncSocialNetworks[] = $socialNetwork;
                        }
                    }
                }

                $post->socialNetworks()->sync($syncSocialNetworks);

                $info_new_task = '';
                $formattText = $this->formatPostText($temp_task->text);
                (!is_null($temp_task->journalist_id)) ? $user_journalist = User::find($temp_task->journalist_id)->name :  $user_journalist = 'Без назначения';
                $info_new_task .= 'Задача <b>'. $temp_task->title.'</b> создана!' . "\n"
                    . '<b>Тезисы:  </b>' . "\n"
                    . $formattText . "\n"
                    . "Дедлайн: " . $deadline_post . "\n"
                    . '<b>Исполнитель: </b>' .  $user_journalist . "\n";

                if($temp_task->posting) {
                    $info_new_task  .= '<b>Соц-сети:</b> ';
                    ($temp_task->posting_to_vk) ? $info_new_task .= 'ВК, ' : '';
                    ($temp_task->posting_to_ok) ? $info_new_task .= 'OK, ' : '';
                    ($temp_task->posting_to_fb) ? $info_new_task .= 'FB, ' : '';
                    ($temp_task->posting_to_ig) ? $info_new_task .= 'IG, ' : '';
                    ($temp_task->posting_to_y_dzen) ? $info_new_task .= 'Я.Дзен, ' : '';
                    ($temp_task->posting_to_y_street) ? $info_new_task .= 'Я.Районы, ' : '';
                    ($temp_task->posting_to_yt) ? $info_new_task .= 'YT, ' : '';
                    ($temp_task->posting_to_tg) ? $info_new_task .= 'TG, ' : '';
                    ($temp_task->posting_to_tt) ? $info_new_task .= 'TT, ' : '';

                    $info_new_task = Str::replaceLast(', ','',$info_new_task);
                    $info_new_task .= "\n";


                    if($temp_task->targeting) {
                        $info_new_task  .= "<b>Тарегтированная реклама:</b> Да \n";

                    }

                    if($temp_task->seeding) {
                        $info_new_task  .= "<b>Посевы:</b> Да \n";
                    }

                    if($temp_task->commenting) {
                        $info_new_task  .= "<b>Комментирование:</b> Да  \n";

                    }

                }
                $info_new_task  .= "<b>Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a></b> \n";
                $temp_task->delete();
                $completeMessage['text'] = $info_new_task;
                (new TelegramBotController(new Api()))->storeMessage($post);
                break;
        }

        return $completeMessage;
    }

    /**
     * Генерация шаблонной клавиатуры с выбором будущего задачи
     * @param $keyboard
     * @return mixed
     */
    private function getKeyboardActions($keyboard) {
        $keyboard->row(Keyboard::inlineButton(['text' => 'Да' , 'callback_data' => 'yes']))
            ->row(Keyboard::inlineButton(['text' => 'Нет', 'callback_data' => 'no']))
            ->row(Keyboard::inlineButton(['text' => 'Завершить постановку задачи', 'callback_data' => 'end_create']));

        return $keyboard;
    }

    private function formatPostText($text)
    {
        $formattText = strip_tags($text, '<b></b><strong></strong><i></i><em></em><a></a><code></code><pre></pre>');
        $formattText = str_replace('</p>', '\n', $formattText);
        $formattText = str_replace('<p>', '', $formattText);

        //
        return $formattText;
    }

}
