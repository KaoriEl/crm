<?php


namespace App\Http\Controllers\Telegram\Services\Journalist;


use App\Models\Comment;
use App\Http\Controllers\BotUsers\EditorController;
use App\Http\Controllers\Telegram\Services\InterfaceWorkWithTask;
use App\Models\Post;
use App\Models\Telegram_user;
use Carbon\Carbon;
use Telegram\Bot\Keyboard\Keyboard;

class JournalistWorkWithTask implements InterfaceWorkWithTask
{
    private $keyboard;
    private $editor;

    public function __construct()
    {
        $this->keyboard =  Keyboard::make()
            ->inline();
        $this->editor = new EditorController();
    }

    /**
     * Работаем с задачами
     * @param $user
     * @param $message
     * @return array
     */
    public function workWithTask($user, $message) : array {
        $completeMessage = [];
        $steps = ['status_','take_in_work_', 'take_tasks', 'give_draft_url_', 'yes_comment', 'no_comment', 'give_publication_url_', 'cancel_action'];
        /**
         * TODO: $steps = это коллбеки, которые приходят от телеграм при нажатии например взять работу
         * Вот список коллбеков:
         * status_ - статусы получаем
         * take_in_work - берем задачу в работу
         * take_tasks - берем определенную задачу без назначения на журналиста
         * give_draft_url_ - отправляем материал по задаче
         * yes_comment - да, если нужно отправить комментарий на материал
         * no_comment - нет, если нельзя отправить комментарий на материал
         * give_publication_url - отправляем ссылку на публикацию
         * cancel_action - отменяем
         */
        $telegram_user = Telegram_user::get()->where('telegram_id', $user['telegram_id'])->first();

        // отмена действия
        if($message->isType('callback_query') && stripos($message->getCallbackQuery()->getData(), $steps[7]) !== false && $telegram_user->post_id != null) {
            $telegram_user->changeStepsUser(null, null);
            $telegram_user->post_id = null;
            $telegram_user->save();
            $completeMessage['text'] = 'Действие отменено.';
            return $completeMessage;
        }

        // Взять задачу в работу
        if($message->isType('callback_query') && stripos($message->getCallbackQuery()->getData(), $steps[1]) !== false) {
            $completeMessage = $this->takeTask($message->getCallbackQuery()->getData(), $telegram_user, $user['obj_user']);
            return $completeMessage;
        }
        ///////////////////////////////////

        // Если отправка ссылки на материал
        if($message->isType('callback_query') && stripos($message->getCallbackQuery()->getData(), $steps[3]) !== false) {
            $completeMessage = $this->addDraftUrl($telegram_user, $message->getCallbackQuery()->getData());
            return $completeMessage;
        }

        if(isset($message['message']['text']) && $telegram_user->current_step == 'wait_draft_link' && $telegram_user->forward_step == 'give_draft_url' ) {
            $completeMessage = $this->addDraftUrl($telegram_user, null,  $message['message']['text']);
            return $completeMessage;
        }

        if($message->isType('callback_query') && stripos($message->getCallbackQuery()->getData(), $steps[4]) !== false) {
            $completeMessage = $this->addDraftUrl($telegram_user, $message->getCallbackQuery()->getData());
            return $completeMessage;
        }

        if($message->isType('callback_query') && stripos($message->getCallbackQuery()->getData(), $steps[5]) !== false) {
            $completeMessage = $this->addDraftUrl($telegram_user, $message->getCallbackQuery()->getData());
            return $completeMessage;
        }


        if(isset($message['message']['text']) && $telegram_user->current_step == 'wait_comment' && $telegram_user->forward_step == 'yes_comment' ) {
            $completeMessage = $this->addDraftUrl($telegram_user, null, $message['message']['text']);
            return $completeMessage;
        }
        ///////////////////////////// Конец добавление ссылки на материал + комментария //////////////////////
        ///

        /**
         * Добавляем ссылку на публикацию
         */
        if($message->isType('callback_query') && stripos($message->getCallbackQuery()->getData(), $steps[6]) !== false) {
            $completeMessage = $this->addPublicationUrl($telegram_user, $message->getCallbackQuery()->getData());
            return $completeMessage;
        }

        if(isset($message['message']['text']) && $telegram_user->current_step == 'send_publication_url' && $telegram_user->forward_step == 'give_publication_url') {
            $completeMessage = $this->addPublicationUrl($telegram_user, null, $message['message']['text']);
            return $completeMessage;
        }

        /// конец добавления ссылки на публикацию

        return $completeMessage;
    }

    /**
     * Назначаем себя ответственным по задаче
     * @param $callback_data
     * @param $telegram_user
     * @param $user
     * @return array
     */
    private function takeTask($callback_data, $telegram_user, $user) {
        $completeMessage = [];
        $take_id_post = $callback_data;
        $take_id_post = str_replace('take_in_work_', "", $take_id_post);

        $post = Post::all()->where('id', $take_id_post)->first();


        if($post->journalist_id != null && $post->journalist_id != $user->id) {
            $completeMessage['text'] = 'У этой задачи уже есть исполнитель';
            return $completeMessage;
        }

        $this->keyboard->row(Keyboard::inlineButton(['text' => 'Отправить ссылку на материал' , 'callback_data' => 'give_draft_url_'.$post->id]));
        $telegram_user->changeStepsUser('take_in_work', null);
        $post->journalist_id = $user->id;
        $post->status_task = true;
        $post->status_id = 3;
        $post->save();

        $text = 'Вы взяли задачу <b>('. $post->title .' - ' . $post->id .') </b> в работу';
        $completeMessage['text'] = $text;
        $completeMessage['keyboard'] = $this->keyboard;

        $this->editor->NotificationEditor($take_id_post, 'journalist_take_task');

        return $completeMessage;
    }

    /**
     * Добавляем ссылку на материал
     * @param $telegram_user
     * @param null $callback_data
     * @param null $text
     * @return array
     */
    private function addDraftUrl($telegram_user, $callback_data = null, $text = null) {
        $completeMessage = [];
        $keyboard = Keyboard::make()
            ->inline();

        if(stripos($callback_data, 'give_draft_url_') !== false) {
            $take_id_post = $callback_data;
            $take_id_post = str_replace('give_draft_url_', "", $take_id_post);

            $keyboard->row(Keyboard::inlineButton(['text' => 'Отменить действие' , 'callback_data' => 'cancel_action']));

            $post = Post::all()->where('id',$take_id_post)->first();
            $telegram_user->changeStepsUser('wait_draft_link', 'give_draft_url');
            $telegram_user->post_id = $take_id_post;
            $telegram_user->save();

            $completeMessage['text'] = 'Ожидание ссылки на материал для задачи: ' . $post->title;
            $completeMessage['keyboard'] = $keyboard;
        } elseif($text && $telegram_user->current_step == 'wait_draft_link') {
            $post = Post::all()->where('id', $telegram_user->post_id)->first();

            $keyboard->row(['text' => 'Да' , 'callback_data' => 'yes_comment'], ['text' => 'Нет' , 'callback_data' => 'no_comment']);
            $telegram_user->changeStepsUser('need_comment', 'wait_draft_link');

            $completeMessage['text'] = 'Нужно добавить комментарий?';
            $completeMessage['keyboard'] = $keyboard;

            $post->approved = null;
            $post->on_moderate = 0;
            $post->status_id = 4;
            $post->draft_url = $text;
            $post->save();
        }


        if($callback_data == 'yes_comment') {
            $telegram_user->changeStepsUser('wait_comment', 'yes_comment');
            $keyboard->row(Keyboard::inlineButton(['text' => 'Отменить действие' , 'callback_data' => 'cancel_action']));
            $completeMessage['text'] = 'Напишите комментарий к материалу';
            $completeMessage['keyboard'] = $keyboard;

        } elseif($callback_data == 'no_comment') {
            $this->editor->NotificationEditor($telegram_user->post_id, 'journalist_give_draft_url');
            $telegram_user->changeStepsUser('send_draft_url', 'no_comment');
            $telegram_user->post_id = null;
            $telegram_user->save();
            $completeMessage['text'] = 'Ссылка на материал отправлена';
        }

        if($telegram_user->current_step == 'wait_comment' && !is_null($text)) {
            $post = Post::get()->where('id',$telegram_user->post_id)->first();
            $telegram_user->changeStepsUser(null, null);
            $telegram_user->post_id = null;
            $telegram_user->save();

            $comment = new comment([
                'text' => $text,
                'role' => 'Журналист',
                'post_id' => $post->id,
            ]);
            $comment->save();
            $completeMessage['text'] = 'Комментарий и ссылка на материал успешно отправлены редактору';
            $this->editor->NotificationEditor($post->id, 'journalist_give_draft_url');
        }


        return $completeMessage;
    }

    /**
     * Добавляем ссылку на публикацию
     * @param $telegram_user
     * @param null $callback_data
     * @param null $text
     * @return array
     */
    private function addPublicationUrl($telegram_user, $callback_data = null, $text = null) {
        $completeMessage = [];
        $keyboard = Keyboard::make()
            ->inline();

        if($callback_data) {
            $take_id_post = $callback_data;
            $take_id_post = str_replace('give_publication_url_', "", $take_id_post);
            $keyboard->row(Keyboard::inlineButton(['text' => 'Отменить действие' , 'callback_data' => 'cancel_action']));

            $post = Post::get()->where('id',$take_id_post)->first();
            $telegram_user->changeStepsUser('send_publication_url', 'give_publication_url');
            $telegram_user->post_id = $take_id_post;
            $telegram_user->save();
            $completeMessage['text'] = 'Ожидание ссылки на публикацию материала для задачи: ' . $post->title;
            $completeMessage['keyboard'] = $keyboard;

        } elseif($text) {
            $post = Post::all()->where('id', $telegram_user->post_id)->first();
            $telegram_user->changeStepsUser(null, null);
            $telegram_user->post_id = null;
            $telegram_user->save();
            $completeMessage['text'] = 'Материал опубликован! Задача завершена';
            $post->publication_url = $text;
            $post->publication_url_updated_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
            $post->status_id = 7;
            $post->save();
            $this->editor->NotificationEditor($post->id, 'journalist_give_publication_url');
        }

        return $completeMessage;
    }
}
