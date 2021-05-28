<?php


namespace App\Http\Controllers\Telegram\Traits\Editor;


use App\Models\Comment;
use App\Http\Controllers\TelegramBotController;
use App\Models\Post;
use App\Models\User;
use App\Models\Telegram_user;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Api;

trait CheckDraftFromAJournalist
{
    /**
     * Проверям материал журналиста
     * @param $id_post
     * @param $telegram_user
     * @param bool|null $onModerating
     * @param bool $onPublication
     * @return array
     */
    public function updateDraftJournalist($id_post, $telegram_user, bool $onModerating = null, bool $onPublication = null) {
        $completeMessage = [];

        $post = Post::find($id_post);


        if($onModerating) {
            $completeMessage['text'] = 'Добавьте комментарий';
            $telegram_user->changeStepsUser('wait_moderating_comment', 'add_task_to_moderate');
            $telegram_user->post_id = $id_post;
            $telegram_user->save();

            $post->approved = false;
            $post->on_moderate = true;
            $post->status_id = 5;
            $post->save();
        } elseif($onPublication) {

            $count_posts = Post::all()->whereNull('archived_at')->where('editor_id', $post->editor_id)->count();

            $keyboard = Keyboard::make()
                ->inline();
            $keyboard->row(
                Keyboard::inlineButton(['text' => 'Список задач (' . $count_posts . ')' , 'callback_data' => 'take_tasks']));

            $text_to_editor = 'Материал по задаче <b>' . $post->id . ' - ' . $post->title . '</b> принят, ожидается ссылка на публикацию.'
                . " Посмотреть задачу: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a>";


            $text_to_editor = $this->formatPostText($text_to_editor);

            $completeMessage['text'] = $text_to_editor;
            $completeMessage['keyboard'] = $keyboard;

            (new TelegramBotController(new Api()))->storeMessageModerateApprove($post);
            $post->approved = true;
            $post->on_moderate = false;
            $post->status_id = 6;
            $post->save();
            $telegram_user->changeStepsUser(null, null);
        }

        return $completeMessage;
    }

    /**
     * Добавляем комментарий при постановки на модерацию
     * @param $id_post
     * @param $telegram_user
     * @param $text
     * @return array
     */
    public function addCommentToModerate($id_post, $telegram_user, $text) {
        $post = Post::find($id_post);
        $completeMessage = [];

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
            Keyboard::inlineButton(['text' => 'Список задач (' . $count_posts . ')' , 'callback_data' => 'take_tasks']));


        $text_to_editor = $this->formatPostText($text_to_editor);
        $telegram_user->changeStepsUser(null, null);
        $telegram_user->post_id = null;
        $telegram_user->save();

        $completeMessage['text'] = $text_to_editor;
        $completeMessage['keyboard'] = $keyboard;

        (new TelegramBotController(new Api()))->storeMessageModerateRework($post);

        return $completeMessage;
    }


}
