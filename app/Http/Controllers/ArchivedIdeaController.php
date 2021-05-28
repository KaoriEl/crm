<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;

class ArchivedIdeaController extends Controller
{
    /**
     * Показывает список заархивированых идей.
     *
     * @return Response
     * @throws AuthorizationException
     */
    public function index()
    {
        $this->authorize('view-archived', Idea::class);

        $ideas = Idea::archived()->latest()->paginate();

        return view('ideas.archived.index', compact('ideas'));
    }

    /**
     * Архивировать идею
     *
     * @param Idea $idea
     * @return Response
     * @throws AuthorizationException
     */
    public function store(Idea $idea)
    {
        $this->authorize('update', $idea);
        $idea->archive();

        $formattText = $this->formatPostText($idea->text);
        $formattArchiveText = $this->formatPostText($idea->archive_comment);

        $user = User::find($idea->user_id);

        $text = "Ваша идея была отправлена в архив\n"
            . "<b>Автор идеи : </b>\n"
            . "$user->name \n"
            . "<b>Причина архивации : </b>\n"
            . $formattArchiveText . "\n"
            . "<b>Текст идеи: </b>\n"
            . $formattText . "\n";

        try {
            (new TelegramBotController(new Api()))->sendMessageToOneUser($user->telegram_id, $text);
        } catch (\Exception  $e) {
            return redirect()->back()->with('message', 'Создатель идеи не найден');
        }


        return redirect()->back();
    }

    /**
     * Восстановить идею из архива.
     *
     * @param Idea $idea
     * @return Response
     * @throws AuthorizationException
     */
    public function destroy(Idea $idea)
    {
        $this->authorize('update', $idea);

        $idea->restore();

        return redirect()->back();
    }
}
