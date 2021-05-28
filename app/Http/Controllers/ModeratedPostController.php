<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\UpdatePostModeratedRequest;
use App\Models\Post;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Telegram\Bot\Api as Api;
use App\Http\Controllers\TelegramBotController as TelegramBot;

/**
 * Отвечает за отправку статьи на модерацию.
 */
class ModeratedPostController extends Controller
{
    protected $telegram;
    protected $telegramBot;
    public function __construct()
    {
        $this->middleware('auth');
        $this->telegram = new Api();
        $this->telegramBot = new TelegramBot($this->telegram);
    }

    /**
     * Отправляет статью на публикацию.
     *
     * @param Post $post
     * @param UpdatePostModeratedRequest $request
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(Post $post, UpdatePostModeratedRequest $request)
    {
        $this->authorize('moderate', $post);

        $validated = $request->validated();

        $post->comment_after_moderating = $validated['comment'];
        $post->approved = true;
        $post->on_moderate = false;
        $post->status_id = 6;
        $post->save();

        $this->telegramBot->storeMessageModerateApprove($post);

        return back()->with('message', 'Статья отправлена на публикацию.');
    }

    /**
     * Отправляет статью на доработку.
     *
     * @param Post $post
     * @param User $user
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function destroy(Post $post, User $user)
    {
        $this->authorize('moderate', $post);

        $post->comment_after_moderating = request('comment');
        $post->approved = false;
        $post->on_moderate = true;
        $post->status_id = 5;
        $post->save();

        Comment::create([
            'text' => request('comment'),
//            'user_id' => $user->id,
            'role' => 'Главный редактор',
            'post_id' => $post->id,
        ]);

        $this->telegramBot->storeMessageModerateRework($post);

        return back()->with('message', 'Публикация отправлена на доработку.');
    }

    /**
     * Удаляет комментарий от главного редактора.
     *
     * @param Post $post
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function uncomment(Post $post)
    {
        $this->authorize('uncomment', $post);

        $post->comment_after_moderating = null;
        $post->save();

        return back()->with('message', 'Комментарий удален.');
    }
}
