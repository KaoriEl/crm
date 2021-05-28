<?php

namespace App\Http\Controllers;

use App\Http\Requests\Idea\StoreIdeaRequest;
use Auth;
use App\Models\Idea;
use App\Models\Post;
use App\Models\User;
use MediaUploader;
use App\Events\IdeaCreated;
use Illuminate\Http\Request;
use App\Notifications\TestNotification;
use cebe\markdown\GithubMarkdown;
use Telegram\Bot\Api;

class IdeaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $ideas = Idea::with('user')->notArchived()->latest()->paginate();

        return view('ideas.index', compact('ideas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $this->authorize('create', Idea::class);

        return view('ideas.create' );
    }
    public function edit(Idea $idea)
    {
        $this->authorize('view', $idea);

        return view('ideas.edit', compact('idea'));
    }

    public function delete()
    {
        $this->authorize('delete', Idea::class);
        return view('ideas.delete');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreIdeaRequest $request)
    {
        $user = auth()->user();

        $this->authorize('create', Idea::class);


        $validated = $request->validated();

        $validated['user_id'] = $user->id;
        $validated['text'] = (new GithubMarkdown())->parse($validated['text']);
        $idea = Idea::create($validated);

        $idea->read_now = request()->get('read_now');
        $idea->save();

        if (isset($validated['files'])) {
            $idea->attachMedia($validated['files'], 'attachment');
        }

        $this->notifyEditors($idea);

        return redirect()->route('ideas.index');
    }

    public function editIdea(StoreIdeaRequest $request, Idea $idea)
    {
        $user = auth()->user();
        $this->authorize('edit', Idea::class);
        $validated = $request->validated();
        $validated['user_id'] = $user->id;
        $validated['text'] = (new GithubMarkdown())->parse($validated['text']);
        $idea->fill($validated);
        $idea->update();

        if (isset($validated['files'])) {
            $idea->attachMedia($validated['files'], 'attachment');
        }

        $this->notifyEditors($idea);

        return redirect()->route('ideas.index');
    }

    public function deleteIdea(Idea $idea)
    {
        $idea->delete();
        return redirect()->route('ideas.index');
    }

    /**
     * Отправляет оповещение всем редакторам, кроме текущего
     * @param Idea $idea
     * @param null $user_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function notifyEditors(Idea $idea, $user_id = null)
    {
        if($user_id == null) {
            $user_id = Auth::id();
        }
        $editorsCount = User::where('id', '<>', $user_id)->role('editor')->count();
        if ($editorsCount > 0) {
            broadcast(new IdeaCreated($idea));
        }
        $editors = User::where('id','<>', $user_id)->role('editor')->get();

        $formattText = $this->formatPostText($idea->text);

        $user = User::find($idea->user_id);

        $needReadNow = ($idea->read_now) ? 'Да ⚡' : 'Нет';

        $text = "Была добавлена новая идея\n"
            . "<b>Автор идеи : </b>" . $user->name . "\n"
            . "<b>Текст идеи: </b>\n"
            . $formattText . "\n"
            . "<b>Срочная идея: </b>"
            . $needReadNow . "\n"
            . "<b>Вы можете открыть идею, перейдя по ссылке: <a href='/ideas/" . $idea->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/ideas/" . $idea->id . "</a></b>";

        foreach($editors as $editor) {
            try {
                (new TelegramBotController(new Api()))->sendMessageToOneUser($editor->telegram_id, $text);
            } catch (\Exception  $e) {
                continue;
            }

        }
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Idea $idea
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Idea $idea)
    {
        $this->authorize('view', $idea);

        return view('ideas.show', compact('idea'));
    }

    private function formatPostText($text)
    {
        $formattText = strip_tags($text, '<b></b><strong></strong><i></i><em></em><a></a><code></code><pre></pre><br /><br><style></style>');
        $formattText = str_replace('</p>', ' \n ', $formattText);
        $formattText = str_replace('<p>', '', $formattText);
//        $formattText = str_replace('<style>', '', $formattText);
        $formattText = str_replace('<br />', "&nbsp;", $formattText);
        //
        return $formattText;
    }

}
