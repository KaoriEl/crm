<?php


namespace App\Http\Controllers\Telegram\Pipes;

use App\Models\Post;
use App\Models\Telegram_user;
use Illuminate\Support\Str;
use Telegram\Bot\Keyboard\Keyboard;
use App\Models\User;

class GetDetailInfoByPost
{

    const ROLE_EDITOR = 4;
    const ROLE_JOURNALIST = 5;

    const STATUSES_TASK = [
        'status_not_journalist' => 2,
        'status_not_work' => 1,
        'status_in_work' => 3,
        'status_wait_check' => 4,
        'status_in_completion' => 5,
        'status_wait_public' => 6,
    ];

    private $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function __invoke($returnData)
    {
        if($returnData['step'] == 'end') {
            return $returnData;
        }
        $telegram_user = Telegram_user::get()->where('telegram_id',$returnData['user']['telegram_id'])->first();

        if(isset($this->message['message']) && strpos($this->message['message']['text'],'/post') !== false && !$this->message->isType('callback_query') && $telegram_user->current_step != 'give_description_idea') {
            $take_id_post = $this->message['message']['text'];
            $take_id_post = str_replace('/post', "", $take_id_post);

            $post = Post::find($take_id_post);
            $completeMessage = $this->getTextDetailTask($post, $telegram_user);

            if(isset($completeMessage['keyboard']) && $completeMessage['keyboard']->count() > 0) {
                $returnData['keyboard']['isset'] = true;
                $returnData['keyboard']['obj_keyboard'] = $completeMessage['keyboard'];
            }
            $returnData['text'] = $completeMessage['text'];
            $returnData['step'] = 'end';
        }

         return $returnData;

    }

    /**
     * Генерируем текст детального поста
     * @param $post
     * @param $telegram_user
     * @return array
     */
    private function getTextDetailTask($post, $telegram_user) {

        $formattText = $this->formatPostText($post->text);
        $deadline_post = $post->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';
        $keyboard = Keyboard::make()
            ->inline();


        $text = "Задача № " . $post->id . "\n"
            . "<b>Название: </b> " . $post->title . " \n"
            . "<b>Тезисы: </b> "
            . $formattText . "\n"
            . "<b>Дедлайн: </b>" . $deadline_post . "\n";

        if($post->journalist != null) {
            $text .= "<b>Исполнитель: </b>" . $post->journalist->name . "\n";
        }

        if($telegram_user->role_id == GetDetailInfoByPost::ROLE_EDITOR && $post->status_id == GetDetailInfoByPost::STATUSES_TASK['status_wait_check'] ) {
            $text .= "<b>Ссылка на материал: </b><a href='" . $post->draft_url . "'>" . $post->draft_url . "</a> \n";

            $keyboard->row(
                Keyboard::inlineButton(['text' => 'Принять материал', 'callback_data' => 'accept_draft_url_'.$post->id]))
                ->row(
                    Keyboard::inlineButton(['text' => 'Отправить на доработку' , 'callback_data' => 'go_to_moderating_'.$post->id]));
            $completeMessage['keyboard'] = $keyboard;
        }

        if($post->status_id == GetDetailInfoByPost::STATUSES_TASK['status_wait_public']) {
            $text .= "<b>Ссылка на материал: </b><a href='" . $post->draft_url . "'>" . $post->draft_url . "</a> \n";
        }

        $status_after_posting = '';
        if($telegram_user->role_id == GetDetailInfoByPost::ROLE_EDITOR && $post->publication_url && $post->posting && !$post->smm_id) {
            $social_network = '';

            $social_network .= ($post->posting_to_vk) ? 'ВК,' : '';
            $social_network .=  ($post->posting_to_ok) ? 'ОК,' : '';
            $social_network .= ($post->posting_to_fb) ? 'FB,' : '';
            $social_network .= ($post->posting_to_ig) ?  'IG,' : '';
            $social_network .=  ($post->posting_to_tg) ?  'TG,' : '';
            $social_network .= ($post->posting_to_yt) ?  'YT,' : '';
            $social_network .= ($post->posting_to_tt) ?  'TT,' : '';
            $social_network .= ($post->posting_to_y_street) ? 'Я.Районы,' : '';
            $social_network .=  ($post->posting_to_y_dzen) ?  'Я.Дзен,' : '';

            $social_network = Str::replaceLast(',','',$social_network);
            $status_after_posting = '';
            $status_after_posting .= ($post->seeding && !$post->seeder_id) ? 'ожидает посева,' : '';
            $status_after_posting .= ($post->targeting && !$post->target_id) ? 'ожидает тарегтированной рекламы,' : '';
            $status_after_posting .= ($post->commenting && !$post->commentator_id) ? 'ожидает комментирования,' : '';

            $status_after_posting = Str::replaceLast(',','',$status_after_posting);

            $text .= "<b>Ссылка на публикацию:</b> <a href='" . $post->draft_url . "'>" . $post->draft_url . "</a> \n"
            . "<b>Соц-сети: </b>" . $social_network . "\n";
        }

        if($telegram_user->role_id == GetDetailInfoByPost::ROLE_EDITOR && $post->smm_id && ($post->seeding || $post->targeting || $post->commenting)) {
            $social_network = '';
            $social_network .= ($post->posting_to_vk) ? 'ВК,' : '';
            $social_network .=  ($post->posting_to_ok) ? 'ОК,' : '';
            $social_network .= ($post->posting_to_fb) ? 'FB,' : '';
            $social_network .= ($post->posting_to_ig) ?  'IG,' : '';
            $social_network .=  ($post->posting_to_tg) ?  'TG,' : '';
            $social_network .= ($post->posting_to_yt) ?  'YT,' : '';
            $social_network .= ($post->posting_to_tt) ?  'TT,' : '';
            $social_network .= ($post->posting_to_y_street) ? 'Я.Районы,' : '';
            $social_network .=  ($post->posting_to_y_dzen) ?  'Я.Дзен,' : '';


            $status_after_posting = '';
            $status_after_posting .= ($post->seeding && !$post->seeder_id) ? 'ожидает посева,' : '';
            $status_after_posting .= ($post->targeting && !$post->target_id) ? 'ожидает тарегтированной рекламы,' : '';
            $status_after_posting .= ($post->commenting && !$post->commentator_id) ? 'ожидает комментирования,' : '';

            $status_after_posting = Str::replaceLast(',','',$status_after_posting);
            $social_network = Str::replaceLast(',','',$social_network);

            $text .= "<b>Ссылка на публикацию:</b> <a href='" . $post->draft_url . "'>" . $post->draft_url . "</a> \n"
                . "<b>Соц-сети: </b>" . $social_network . "\n";

        }

        if($telegram_user->role_id == GetDetailInfoByPost::ROLE_JOURNALIST) {
            $completeMessage['keyboard'] = $this->generateKeyboardJournalist($post);
        }

        if(empty($status_after_posting)) {
            $text .= '<b>Статус задачи: </b>' . $post->status->title;
        } else {
          $text .= '<b>Статус задачи: </b>' . $status_after_posting;
        }

        $completeMessage['text'] = $text;




        return $completeMessage;
    }

    /**
     * Очищаем текст задачи
     * @param $text
     * @return string|string[]
     */

    private function formatPostText($text)
    {
        $formattText = strip_tags($text, '<b></b><strong></strong><i></i><em></em><a></a><code></code><pre></pre>');
        $formattText = str_replace('</p>', '\n', $formattText);
        $formattText = str_replace('<p>', '', $formattText);

        //
        return $formattText;
    }

    /**
     * Журналист имеет много возможностей взаимодействовать с задачей
     * и поэтому формируем кнопки для задачи с определенном статусом, чтобы журналист мог работать с ней
     * @param $post
     * @return Keyboard
     */
    private function generateKeyboardJournalist($post) {
        $keyboard = Keyboard::make()
            ->inline();

        switch($post->status_id) {
            case GetDetailInfoByPost::STATUSES_TASK['status_not_journalist']:
                $keyboard->row(
                    Keyboard::inlineButton(['text' => 'Взять задачу в работу' , 'callback_data' => 'take_in_work_'.$post->id]));
                break;
            case GetDetailInfoByPost::STATUSES_TASK['status_not_work']:
                $keyboard->row(
                    Keyboard::inlineButton(['text' => 'Начать работу' , 'callback_data' => 'take_in_work_'.$post->id]));
                break;
            case GetDetailInfoByPost::STATUSES_TASK['status_in_work']:
                $keyboard->row(
                    Keyboard::inlineButton(['text' => 'Отправить ссылку на материал' , 'callback_data' => 'give_draft_url_'.$post->id]));
                break;
            case GetDetailInfoByPost::STATUSES_TASK['status_in_completion']:
                $keyboard->row(
                    Keyboard::inlineButton(['text' => 'Отправить ссылку на публикацию' , 'callback_data' => 'give_draft_url_'.$post->id]));
                break;
            case GetDetailInfoByPost::STATUSES_TASK['status_wait_public']:
                $keyboard->row(
                    Keyboard::inlineButton(['text' => 'Отправить ссылку на публикацию' , 'callback_data' => 'give_publication_url_'.$post->id]));
                break;


        }

        return $keyboard;
    }

}
