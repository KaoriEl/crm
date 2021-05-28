<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\UpdatePostAssigneeRequest;
use App\Mail\sendByMail;
use App\Models\Post;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Ответственен за назначение исполнителя для поста.
 */
class PostAssigneeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Назначает исполнителя на пост.
     *
     * @param Post $post
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function store(Post $post)
    {
        $this->authorize('set-assignee', $post);

        $validated = request()->validate([
            'journalist_id' => 'required|exists:users,id',
        ]);

        if ($post->journalist_id !== null) {
            return back()->with('error', 'На публикацию уже назначен исполнитель');
        }

        // если журналист пытается назначить кого-то кроме себя
        if (auth()->user()->hasRole('journalist') && auth()->id() !== (int) $validated['journalist_id']) {
            return back()->with('error', 'У вас недостаточно прав для назначения других пользователей на публикацию');
        }

        // если редактор пытается назначить сам себя в качестве исполнителя
        if (auth()->user()->hasRole('editor') && auth()->id() === (int) $validated['journalist_id']) {
            return back()->with('error', 'Вы не можете назначить себя в качестве исполнителя');
        }

        $post->journalist_id = $validated['journalist_id'];
        $post->save();

        return back()->with('message', 'Исполнитель успешно назначен');
    }

    /**
     * Изменяет исполнителя на пост.
     *
     * @param Post $post
     * @param UpdatePostAssigneeRequest $request
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(Post $post, UpdatePostAssigneeRequest $request)
    {
        $this->authorize('set-assignee', $post);

        $validated = $request->validated();

        // если журналист пытается назначить кого-то кроме себя
            if (!(auth()->user()->hasRole('admin')) || !(auth()->user()->hasRole('editor')) && auth()->id() !== (int) $validated['journalist_id']) {
           return back()->with('error', 'У вас недостаточно прав для назначения других пользователей на публикацию');
      }

        // если редактор пытается назначить сам себя в качестве исполнителя
           if (auth()->user()->hasRole('editor') && auth()->id() === (int) $validated['journalist_id']) {
            return back()->with('error', 'Вы не можете назначить себя в качестве исполнителя');
      }

        $post->journalist_id = $validated['journalist_id'];
        $post->save();

//        if ($post->journalist->email) {
//            Mail::to($post->journalist->email)
//                ->send(new sendByMail($post));
//        }

        app('App\Http\Controllers\TelegramBotController')->sendMessageChangedJournalist($post);

        return back()->with('message', 'Исполнитель успешно назначен');
    }
}
