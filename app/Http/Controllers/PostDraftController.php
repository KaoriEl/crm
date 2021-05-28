<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Controllers\Excel\WorkWithExcel;
use App\Http\Requests\Post\UpdatePostDraftRequest;
use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Telegram\Bot\Api;
use App\Http\Controllers\TelegramBotController as TelegramBot;
use Telegram\Bot\Keyboard\Keyboard;

class PostDraftController extends Controller
{
    protected $telegramBot;
    protected $telegram;
    public function __construct(Api $telegram)
    {
        $this->middleware('auth');
        $this->telegram = $telegram;
        $this->telegramBot = new TelegramBot($telegram);
    }

    /**
     * Добавляет ссылку на черновик к посту.
     * @param Post $post
     * @param UpdatePostDraftRequest $request
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(Post $post, UpdatePostDraftRequest $request)
    {
        $this->authorize('set-draft', $post);

        $validated = $request->validated();

       $post->draft_url = $validated['draft_url'];
        $post->approved = null;
        $post->status_id = 4;
        $post->on_moderate = false;
        $post->save();

        Comment::create([
            'text' => request('comment'),
//            'user_id' => $user->id,
            'role' => 'Журналист',
            'post_id' => $post->id,
        ]);


        $formattText = $this->formatPostText($post->text);

        $comments = '';
        foreach($post->comments as $value) {
            if($value->role == 'Журналист') {
                $comments .= "<b>Комментарий журналиста: </b>" . $value->text . "\n";
            } else {
                $comments .= "<b>Комментарий редактора: </b>" . $value->text . "\n";
            }
        }
        $deadline_post = $post->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';

        $text = "Обновлена задача\n"
            . "<b>Материал был добавлен к задаче: </b>\n"
            . "$post->id - $post->title\n"
            . "<b>Тезисы: </b>\n"
            . $formattText . "\n"
            . "Дедлайн: " . $deadline_post . "\n"
            . $comments
            . "<b>Ссылка на материал: </b><a href='" . $post->draft_url . "'>" . $post->draft_url . "</a> \n"
            . "<b>Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a></b>";


        $user = User::all()->where('id', $post->editor_id)->first();

        $keyboard = Keyboard::make()
            ->inline()
            ->row(
                Keyboard::inlineButton(['text' => 'Принять материал' , 'callback_data' => 'accept_draft_url_'.$post->id]))
            ->row(
                Keyboard::inlineButton(['text' => 'Отправить на доработку' , 'callback_data' => 'go_to_moderating_'.$post->id]));

        $this->telegramBot->sendMessageToOneUser($user->telegram_id, $text, $keyboard);
        $this->telegramBot->editor->NotificationEditor($post->id, 'journalist_give_draft_url');
        return back()->with('message', 'Публикация отправлена на проверку.');
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
