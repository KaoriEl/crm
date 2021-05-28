<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Excel\WorkWithExcel;
use App\Http\Controllers\Instagram\ParsingInstagram;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Requests\Post\UpdateStatusPostRequest;
use App\Models\Post;
use App\Models\Idea;
use App\Models\Project;
use App\Models\SmmLink;
use App\Models\SocialNetwork;
use App\Models\ModelsSeedLinks;
use App\Models\StatisticSocialNetwork;
use App\Models\User;
use App\Pipes\Posts\UpdateCommentPipe;
use App\Pipes\Posts\UpdatePostingPipe;
use App\Pipes\Posts\UpdateSeedingPipe;
use App\Pipes\Posts\UpdateTargetPipe;
use App\UseCases\Posts\CrudPostUseCase;
use cebe\markdown\GithubMarkdown;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Pipeline\Pipeline;
use Illuminate\View\View;
use MediaUploader;
use Carbon\Carbon;
use App\Events\PostCreated;
use App\Mail\sendByMail;
use App\Http\Requests\Post\StorePost;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Api;
use App\Http\Controllers\TelegramBotController as TelegramBot;

class PostController extends Controller
{
    protected $telegramBot;
    private $telegram;

    public function __construct(Api $telegram)
    {
        $this->middleware('auth');

        $this->telegramBot = new TelegramBot($telegram);
        $this->telegram = new TelegramBotController(new Api());

    }

    /**
     * Отфильтровывает список постов в зависимости от прав пользователей.
     *
     * @param Post[] $posts
     * @return Post[]
     */
    private function filter($posts)
    {
        $isPostingRequired = Auth::user()->hasRole('smm');
        $isSeedingRequired = Auth::user()->hasRole('seeder');
        $isCommercialSeederingRequired = Auth::user()->hasRole('seeder');
        $isTargetingRequired = Auth::user()->hasRole('targeter');
        $isCommentingRequired = Auth::user()->hasRole('commenter');

        return $posts->filter(function (Post $post) use (
            $isCommentingRequired, $isSeedingRequired, $isPostingRequired,
            $isTargetingRequired,$isCommercialSeederingRequired
        ) {

            if ($post->commenting && $isCommentingRequired && !$post->commented) {
                return true;
            }

            if ($post->seeding && $isSeedingRequired && !$post->seed_list_url) {
                return true;
            }

            if ($post->posting && $isPostingRequired) {
                if ($post->posting_to_vk && !$post->vk_post_url) {
                    return true;
                }

                if ($post->posting_to_ok && !$post->ok_post_url) {
                    return true;
                }

                if ($post->posting_to_fb && !$post->fb_post_url) {
                    return true;
                }

                if ($post->posting_to_ig && !$post->ig_post_url) {
                    return true;
                }

                if ($post->posting_to_y_dzen && !$post->y_dzen_post_url) {
                    return true;
                }

                if ($post->posting_to_y_street && !$post->y_street_post_url) {
                    return true;
                }

                if ($post->posting_to_yt && !$post->yt_post_url) {
                    return true;
                }

                if ($post->posting_to_tg && !$post->tg_post_url) {
                    return true;
                }

                if ($post->posting_to_tt && !$post->tt_post_url) {
                    return true;
                }
            }  if ($post->commercial_seed && $isCommercialSeederingRequired) {
                if ($post->commercial_seed_to_vk && !$post->vk_post_url) {
                    return true;
                }

                if ($post->commercial_seed_to_ok && !$post->ok_post_url) {
                    return true;
                }

                if ($post->commercial_seed_to_fb && !$post->fb_post_url) {
                    return true;
                }

                if ($post->commercial_seed_to_ig && !$post->ig_post_url) {
                    return true;
                }

                if ($post->commercial_seed_to_y_dzen && !$post->y_dzen_post_url) {
                    return true;
                }

                if ($post->commercial_seed_to_yt && !$post->yt_post_url) {
                    return true;
                }

                if ($post->commercial_seed_to_tg && !$post->tg_post_url) {
                    return true;
                }

                if ($post->commercial_seed_to_tt && !$post->tt_post_url) {
                    return true;
                }
            }

            if ($post->targeting && $isTargetingRequired) {
                if ($post->targeting_to_vk && (!$post->targeted_to_vk || !$post->target_launched_in_vk)) {
                    return true;
                }

                if ($post->targeting_to_ok && (!$post->targeted_to_ok || !$post->target_launched_in_ok)) {
                    return true;
                }

                if ($post->targeting_to_fb && (!$post->targeted_to_fb || !$post->target_launched_in_fb)) {
                    return true;
                }

                if ($post->targeting_to_ig && (!$post->targeted_to_ig || !$post->target_launched_in_ig)) {
                    return true;
                }

                if ($post->targeting_to_y_dzen && (!$post->targeted_to_y_dzen || !$post->target_launched_in_y_dzen)) {
                    return true;
                }

                if ($post->targeting_to_y_street && (!$post->targeted_to_y_street || !$post->target_launched_in_y_street)) {
                    return true;
                }

                if ($post->targeting_to_yt && (!$post->targeted_to_yt || !$post->target_launched_in_yt)) {
                    return true;
                }

                if ($post->targeting_to_tg && (!$post->targeted_to_tg || !$post->target_launched_in_tg)) {
                    return true;
                }
            }

            if ($post->targeting && $isTargetingRequired) {
                if ($post->targeting_to_vk && (!$post->targeted_to_vk || !$post->target_launched_in_vk)) {
                    return true;
                }

                if ($post->targeting_to_ok && (!$post->targeted_to_ok || !$post->target_launched_in_ok)) {
                    return true;
                }

                if ($post->targeting_to_fb && (!$post->targeted_to_fb || !$post->target_launched_in_fb)) {
                    return true;
                }

                if ($post->targeting_to_ig && (!$post->targeted_to_ig || !$post->target_launched_in_ig)) {
                    return true;
                }

                if ($post->targeting_to_y_dzen && (!$post->targeted_to_y_dzen || !$post->target_launched_in_y_dzen)) {
                    return true;
                }

                if ($post->targeting_to_y_street && (!$post->targeted_to_y_street || !$post->target_launched_in_y_street)) {
                    return true;
                }

                if ($post->targeting_to_yt && (!$post->targeted_to_yt || !$post->target_launched_in_yt)) {
                    return true;
                }

                if ($post->targeting_to_tg && (!$post->targeted_to_tg || !$post->target_launched_in_tg)) {
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * Отображает страницу со списком постов.
     *
     * @return Factory|View
     */
    public function index()
    {


        $projects_user = Auth::user()->projects()->pluck('id');
        $query = Post::skipArchived()->orderBy('expires_at')->where('editor_id', '!=', 25);

        if (!Auth::user()->hasRole('admin')) {
            $query->whereIn('project_id', $projects_user);
        }

        if (!Auth::user()->hasRole('editor') && !Auth::user()->hasRole('admin')/** && !Auth::user()->hasRole('smm')  */) {
            if (!Auth::user()->hasRole('journalist')) {
                // сюда попадут все, кроме журналиста и гл. редактора
                // только гл. редактор и журналист могут видеть посты, где нет публикации
                // все остальные видят только опубликованные посты
                $query->published();
            } elseif (Auth::user()->hasRole('journalist') && Auth::user()->roles->count() === 1) {
                // если у пользователя только одна роль - Журналист
                // журналист может видеть только те публикации, которые ещё не опубликованы
                $query->skipPublished()->where(function ($q) {
                    // плюс журналист может видеть только те публикации, которые назначены ему,
                    $q->where('journalist_id', Auth::id())
                        // или те, которые вообще никому не назначены
                        ->orWhereNull('journalist_id');
                });
            }
        }

        $posts = $query->get();

//        if(Auth::user()->hasRole('smm') && Auth::user()->roles->count() === 1) {
//            $posts = Post::where('smm_id', Auth::id())->where('archived_at', null)->get();
//            $posts = $posts->merge(Post::where('smm_id', null)->where('archived_at', null)->get());
//        } else {
//            $posts = $query->get();
//        }



        if (!Auth::user()->hasAnyRole(['admin', 'editor', 'journalist'])) {
            $posts = $this->filter($posts);
        }

        $user = auth()->user();

        foreach ($posts as $post) {
            if ($post->done()) {
                $post->archive();
            }
        }

        $arrSMMLinks = [];
        foreach ($posts as $post) {
            foreach(SmmLink::where('post_id', $post->id)->get() as $link) {
                $arrSMMLinks[$link->socialNetwork()->first()->slug][] = $link['link'];
            }
        }

        $arrSeedLinks = [];
        foreach ($posts as $post) {
            foreach(SmmLink::where('post_id', $post->id)->get() as $link) {
                $arrSeedLinks[$link->socialNetwork()->first()->slug][] = $link['link'];
            }
        }

        $arrSMMLinksStatistic = [];
        foreach ($posts as $post) {
        foreach(Post::find($post->id)->getSMMLinks() as $link) {
            foreach (StatisticSocialNetwork::where('post_smm_links_id', $link->id)->get() as $statistic) {
                $arrSMMLinksStatistic[$link->socialNetwork()->first()->slug][] = $statistic;
            }
        }
        }

        $arrSeedLinks = [];
        foreach ($posts as $post) {
            foreach(Post::find($post->id)->getSeedLinks() as $link) {
                foreach (StatisticSocialNetwork::where('post_smm_links_id', $link->id)->get() as $statistic) {
                    $arrSeedLinks[$link->socialNetwork()->first()->slug][] = $statistic;
                }
            }
        }

        return view('posts.index', compact('posts', 'user', "arrSMMLinks", "arrSMMLinksStatistic" , 'arrSeedLinks'));
    }

    /**
     * Показывает страницу создания поста.
     *
     * @return Factory|View
     * @throws AuthorizationException
     */
    public function create()
    {
        $this->authorize('create', Post::class);

        $journalists = User::role('journalist')->get();

        // Идея, на основе которой создается публикация
        $idea = null;
        $files = [];
        $projects = Project::all()->filter(function ($item) {
            return $item['archived_at'] == null;
        });
        $currentProject = $projects->first();

        if (request()->has('idea')) {
            $idea = Idea::find(request('idea'));

            if ($idea) {
                foreach ($idea->getMedia('attachment') as $media) {
                    $fileItem = collect($media->toArray())->only(['id', 'filename', 'size']);
                    $fileItem['full_url'] = $media->getUrl();
                    $files[] = $fileItem;
                }
            }
        }

        $socialNetworks = SocialNetwork::get();
        $socialNetworksSeed = SocialNetwork::get();
        return view('posts.create', compact('currentProject', 'projects', 'journalists', 'idea', 'files', 'socialNetworks', 'socialNetworksSeed'));
    }

    /**
     * Добавляет пост в БД.
     *
     * @param StorePost $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function store(StorePost $request)
    {


        $baseOn = request()->validate([
            'idea_id' => 'nullable|exists:ideas,id',
        ]);

        $associatedFiles = request()->validate([
            'files' => 'sometimes|array',
            'files.*' => 'exists:media,id',
        ]);

        $post = Post::make($request->validated());


        $post->expires_at = $request->expiresAt();

        $post->editor_id = Auth::user()->id;

        if ($post->journalist_id != null) {
            $post->status_id = 1;
        } else {
            $post->status_id = 2;
        }


        // костыль т.к ajax не успевает обрабатываться: если галочка не выбрана размещение в СОЦ.сетях
        if ($post->posting == 0) {
            $post->posting_to_vk = 0;
            $post->posting_to_ok = 0;
            $post->posting_to_fb = 0;
            $post->posting_to_ig = 0;
            $post->posting_to_tg = 0;
            $post->posting_to_yt = 0;
            $post->posting_to_y_street = 0;
            $post->posting_to_y_dzen = 0;
            $post->posting_to_tt = 0;
        }
        /*
        // костыль т.к ajax не успевает обрабатываться: если галочка таргетированная реклама не выбрана, то все по улям
        if($post->targeting == 0) {
            $post->targeting_to_vk = 0;
            $post->targeting_to_ok = 0;
            $post->targeting_to_fb = 0;
            $post->targeting_to_ig = 0;
            $post->targeting_to_tg = 0;
            $post->targeting_to_yt = 0;
            $post->targeting_to_y_street = 0;
            $post->targeting_to_y_dzen = 0;
        }

        // костыль т.к ajax не успевает обрабатываться: если галочка посвевы не выбрана, то все по нулям
        if($post->seeding == 0) {
            $post->seeding_to_vk = 0;
            $post->seeding_to_ok = 0;
            $post->seeding_to_fb = 0;
            $post->seeding_to_insta = 0;
            $post->seeding_to_tg = 0;
            $post->seeding_to_yt = 0;
            $post->seeding_to_y_street = 0;
            $post->seeding_to_y_dzen = 0;
        }
        */
        $post->save();

        if (isset($associatedFiles['files'])) {
            $post->attachMedia($associatedFiles['files'], 'attachment');
        }

        if (isset($baseOn['idea_id'])) {
            $idea = Idea::find($baseOn['idea_id']);
            $idea->delete();
        }
        if ($post->hasJournalist() && $post->journalist->email) {
            Mail::to($post->journalist->email)
                ->send(new sendByMail($post));
        }

        CrudPostUseCase::syncSocialNetworks($post, $request->input('targeting_to', []));

//        CrudPostUseCase::syncCommercialSeedNetworks($post, $request->input('commercial_seed_to', []));

        // Отправить

        app('App\Http\Controllers\TelegramBotController')->storeMessage($post);

        broadcast(new PostCreated($post));

        return redirect()->route('posts.show', $post->id);
    }

    /**
     * Открывает страницу редактирования поста.
     *
     * @param Post $post
     * @return Application|Factory|Response|View
     * @throws AuthorizationException
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        $projects = Project::all()->filter(function ($item) {
            return $item['archived_at'] == null;
        });
        $currentProject = $projects->first();

        $journalists = User::role('journalist')->get();
        $projects = Project::all()->filter(function ($item) {
            return $item['archived_at'] == null;
        });

        $socialNetworks = SocialNetwork::get();
        $socialNetworkSeed = SocialNetwork::get();
        $selectedSocialNetworks = $post->socialNetworks->pluck('id')->toArray();
        $selectedSocialSeedNetworks = $post->seedLinks->pluck('id')->toArray();
//        dd($post->seedLinks);
        return view('posts.edit', compact('projects', 'post', 'journalists', 'currentProject', 'socialNetworks', 'selectedSocialNetworks', 'selectedSocialSeedNetworks', 'socialNetworkSeed'));
    }

    /**
     * Показывает детальную страницу поста.
     *
     * @param Post $post
     * @return Factory|View
     * @throws AuthorizationException
     */
    public function show(Post $post)
    {

        $arrSMMLinks = [];
       foreach(SmmLink::where('post_id', $post->id)->get() as $link) {
           $arrSMMLinks[$link->socialNetwork()->first()->slug][] = $link['link'];
       }

        $arrSeedLinks = [];
        foreach(ModelsSeedLinks::where('post_id', $post->id)->get() as $link) {
            $arrSeedLinks[$link->socialNetwork()->first()->slug][] = $link['link'];
        }

        $arrSocialNetworks = $post->seedLinks()->get()->unique('slug');

        $post['text'] = (new GithubMarkdown())->parse($post['text']);
        $this->authorize('view', $post);
        $comments = $post->comments()->orderBy('created_at', 'desc')->get();

        return view('posts.show', compact('post', 'comments', 'arrSMMLinks', 'arrSeedLinks', 'arrSocialNetworks'));
    }

    /**
     * Вывод в публичку
     *
     * @param Post $post
     * @return Factory|View
     * @throws AuthorizationException
     */


    /**
     * Обновляет информацию о посте (страница редактирования поста).
     *
     * @param StorePost $request
     * @param Post $post
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function put(StorePost $request, Post $post)
    {
        $this->authorize('put', $post);

//        dd($request->validated());
        /*
        if($post->journalist_id == $validated['journalist_id']) {

        } else {

            $this->sendTelegramNotification($post);

            $post->update($validated);
            app('App\Http\Controllers\TelegramBotController')->storeMessage($post);
        }
        */

        if ($post->journalist_id == $request->validated()['journalist_id']) {

        } else {

            app('App\Http\Controllers\TelegramBotController')->storeMessage($post);
        }

        $post->fill($request->validated());
        $post->expires_at = $request->expiresAt();

        $post->save();

        $associatedFiles = request()->validate([
            'files' => 'array',
            'files.*' => 'required|exists:media,id',
        ]);

        if (isset($associatedFiles['files'])) {
            $post->syncMedia($associatedFiles['files'], 'attachment');
        }

        CrudPostUseCase::syncSocialNetworks($post, $request->input('targeting_to', []));
//        CrudPostUseCase::syncCommercialSeedNetworks($post, $request->input('commercial_seed_to', []));
        return back()->with('message', 'Задача успешно обновлена');
    }

    /**
     * Обновляет информацию о публикации.
     * @param Pipeline $pipeline
     * @param Post $post
     * @param UpdatePostRequest $request
     * @return RedirectResponse
     */
    public function update(Pipeline $pipeline, Post $post, UpdatePostRequest $request)
    {

        $validated = $request->validated();

            $tmp_post_links = SmmLink::where('post_id', $post->id)->get();
            $arrSMMLinks = [];
            if(isset($validated['smm_links'])) {
                foreach($validated['smm_links'] as $key => $value) {
                    $id_social_network = SocialNetwork::where('slug', $key)->pluck('id')->first();
                    $post->smmLinks()->where("post_id", $post->id)->detach($id_social_network);
                    foreach($value as $link) {
                        if(!is_null($link['link'])) {
                            if($tmp_post_links->where('link', $link['link'])->count() == 0) {
                                $arrSMMLinks[$key][] = $link['link'];
                            }
                            $post->smmLinks()->attach([$id_social_network => ['link' => $link['link']]]);
                        }
                    }
                }

            }


        $tmp_post_links_seeder = ModelsSeedLinks::where('post_id', $post->id)->get();
        $arrSeedLinks = [];
        if(isset($validated['seed_links'])) {
            foreach($validated['seed_links'] as $key => $value) {
                $id_social_network_for_seeder = SocialNetwork::where('slug', $key)->pluck('id')->first();
                $post->seedLinks()->where("post_id", $post->id)->detach($id_social_network_for_seeder);
                foreach($value as $link) {
                    if(!is_null($link['link'])) {
                        if($tmp_post_links_seeder->where('link', $link['link'])->count() == 0) {
                            $arrSeedLinks[$key][] = $link['link'];
                        }
                        $post->seedLinks()->attach([$id_social_network_for_seeder => ['link' => $link['link']]]);
                    }
                }
            }

        }



        $this->sendTelegramNotification($post, $arrSMMLinks);
        $this->sendTargetNotification($post, $arrSMMLinks);
        $this->sendSeederNotification($post, $arrSMMLinks);


//        $post->update($validated);

        $pipeArgs = [
            'post' => $post,
            'arrSMMLinks' => $arrSMMLinks,
            'arrSeedLinks' => $arrSeedLinks,
            'validated' => $validated
            ];

        $pipeline->send($pipeArgs)
            ->through([
                UpdatePostingPipe::class,
                UpdateSeedingPipe::class,
                UpdateTargetPipe::class,
                UpdateCommentPipe::class
            ])->thenReturn();

        if ($post->done()) {
            $post->archive();
        }

        return back()->with('message', 'Информация успешно обновлена');
    }

    private function sendTargetNotification(Post $post, $smmLinks)
    {
        $targetPrice = Post::find($post->id)->socialNetworks;
        $text = "<b>Поступила новая задача на настройку таргета</b> \n";
        $text .= "$post->id - $post->title\n\n";
        $text .= "<b>Продвижение в соц сетях и бюджеты:</b>";

        $ok_array = [];
        $vk_array = [];
        $tg_array = [];
        $inst_array = [];
        $facebook_array = [];
        $ya_dzen_array = [];
        $youtube_array = [];
        $tt_array = [];
        foreach (SmmLink::where('post_id', $post->id)->get() as $link) {
            $link_to_find = $link->link;
            $find_ok = 'ok.ru';
            $find_vk = 'vk.com';
            $find_tg = 't.me';
            $find_inst = 'instagram.com';
            $find_facebook = 'facebook.com';
            $find_ya_dzen = 'zen.yandex.ru';
            $find_youtube = 'youtu';
            $find_tt = 'tiktok.com';
            $ok = strpos($link_to_find, $find_ok);
            $vk = strpos($link_to_find, $find_vk);
            $tg = strpos($link_to_find, $find_tg);
            $inst = strpos($link_to_find, $find_inst);
            $facebook = strpos($link_to_find, $find_facebook);
            $ya_dzen = strpos($link_to_find, $find_ya_dzen);
            $youtube = strpos($link_to_find, $find_youtube);
            $tt = strpos($link_to_find, $find_tt);
            if ($ok !== false) {
                $ok_array["status"] = "true";
                $ok_array["link"][] = $link->link;
            }
            if ($vk !== false) {
                $vk_array["status"] = "true";
                $vk_array["link"][] = $link->link;
            }
            if ($tg !== false) {
                $tg_array["status"] = "true";
                $tg_array["link"][] = $link->link;
            }
            if ($inst !== false) {
                $inst_array["status"] = "true";
                $inst_array["link"][] = $link->link;
            }

            if ($facebook !== false) {
                $facebook_array["status"] = "true";
                $facebook_array["link"][] = $link->link;
            }


            if ($ya_dzen !== false) {
                $ya_dzen_array["status"] = "true";
                $ya_dzen_array["link"][] = $link->link;
            }
            if ($youtube !== false) {
                $youtube_array["status"] = "true";
                $youtube_array["link"][] = $link->link;
            }
            if ($tt !== false) {
                $tt_array["status"] = "true";
                $tt_array["link"][] = $link->link;
            }
        }

        if (isset($ok_array["status"])) {
            $ok = false;
            foreach ($targetPrice as $target) {
                if ($target["name"] == "ОК") {
                    $ok = true;
                    $text .= "\n" . '<b>' . $target["name"] . ' - ' . $target->getOriginal("pivot_price") . '</b>' . "\n";
                }
            }
            foreach ($ok_array["link"] as $ok_link) {
                if ($ok == true){
                    $text .= '<a href="' . $ok_link . '">' . $ok_link . '</a>' . "\n";;
                }
            }
        }
        if (isset($facebook_array["status"])) {
            $fb = false;
            foreach ($targetPrice as $target) {
                if ($target["name"] == "FB") {
                    $fb = true;
                    $text .= "\n" . '<b>' . $target["name"] . ' - ' . $target->getOriginal("pivot_price") . '</b>' . "\n";
                }
            }
            foreach ($facebook_array["link"] as $facebook_link) {
                if ($fb == true){
                    $text .= '<a href="' . $facebook_link . '">' . $facebook_link . '</a>' . "\n";;
                }
            }
        }
        if (isset($vk_array["status"])) {
            $vk = false;
            foreach ($targetPrice as $target) {
                if ($target["name"] == "ВК") {
                    $vk = true;
                    $text .= "\n" . '<b>' . $target["name"] . ' - ' . $target->getOriginal("pivot_price") . '</b>' . "\n";
                }
            }
            foreach ($vk_array["link"] as $vk_link) {
                if ($vk == true){
                    $text .= '<a href="' . $vk_link . '">' . $vk_link . '</a>' . "\n";;
                }

            }

        }
        if (isset($tg_array["status"])) {
            $tg = false;
            foreach ($targetPrice as $target) {
                if ($target["name"] == "TG") {
                    $tg = true;
                    $text .= "\n" . '<b>' . $target["name"] . ' - ' . $target->getOriginal("pivot_price") . '</b>' . "\n";
                }
            }
            foreach ($tg_array["link"] as $tg_link) {
                if ($tg == true){
                    $text .= '<a href="' . $tg_link . '">' . $tg_link . '</a>' . "\n";;
                }
            }
        }
        if (isset($inst_array["status"])) {
            $ins = false;
            foreach ($targetPrice as $target) {
                if ($target["name"] == "Insta") {
                    $ins = true;
                    $text .= "\n" . '<b>' . $target["name"] . ' - ' . $target->getOriginal("pivot_price") . '</b>' . "\n";
                }
            }
            foreach ($inst_array["link"] as $inst_link) {
                if (  $ins == true){
                    $text .= '<a href="' . $inst_link . '">' . $inst_link . '</a>' . "\n";;
                }
            }

        }
        if (isset($ya_dzen_array["status"])) {
            $dzen = false;
            foreach ($targetPrice as $target) {
                if ($target["name"] == "Я.Д") {
                    $dzen = true;
                    $text .= "\n" . '<b>' . $target["name"] . ' - ' . $target->getOriginal("pivot_price") . '</b>' . "\n";
                }
            }
            foreach ($ya_dzen_array["link"] as $ya_link) {
                if ($dzen == true){
                    $text .= '<a href="' . $ya_link . '">' . $ya_link . '</a>' . "\n";
                }
            }

        }

        if (isset($youtube_array["status"])) {
            $yt = false;
            foreach ($targetPrice as $target) {
                if ($target["name"] == "YT") {
                    $yt = true;
                    $text .= "\n" . '<b>' . $target["name"] . ' - ' . $target->getOriginal("pivot_price") . '</b>' . "\n";
                }
            }
            foreach ($youtube_array["link"] as $yt_link) {
                if ($yt == true) {
                    $text .= '<a href="' . $yt_link . '">' . $yt_link . '</a>' . "\n";
                }
            }
        }
        if (isset($tt_array["status"])) {
            $tt = false;
            foreach ($targetPrice as $target) {
                if ($target["name"] == "TT") {
                    $tt = true;
                    $text .= "\n" . '<b>' . $target["name"] . ' - ' . $target->getOriginal("pivot_price") . '</b>' . "\n";
                }
            }
            foreach ($tt_array["link"] as $tt_link) {
                if ($tt == true) {
                    $text .= '<a href="' . $tt_link . '">' . $tt_link . '</a>' . "\n";
                }
            }
        }

        if (isset($post->targeting_text)) {
            $text .= "\n<b>Комментарий:</b>" . "\n" . $post->targeting_text . "\n";
        }
        $text .= "\n<b>Вы можете открыть задачу, перейдя по ссылке: </b>" . '<a href="/posts/' . $post->id . '">' . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . '</a>';
        $this->telegram->sendMessageToAllInOneRole($text, 'targeter');
    }

    private function sendSeederNotification(Post $post, $smmLinks)
    {
        $text = "<b>Поступила новая задача на посевы</b> \n";
        $text .= "$post->id - $post->title\n\n";
        $text .= "<b>Посевы в соц сетях</b> \n";

        $ok_array = ["link" => [], "active" => 0];
        $vk_array = ["link" => [], "active" => 0];
        $tg_array = ["link" => [], "active" => 0];
        $inst_array = ["link" => [], "active" => 0];
        $facebook_array = ["link" => [], "active" => 0];
        $ya_dzen_array = ["link" => [], "active" => 0];
        $youtube_array = ["link" => [], "active" => 0];
        $tt_array = ["link" => [], "active" => 0];

        foreach (SmmLink::where('post_id', $post->id)->get() as $link) {
            $link_to_find = $link->link;
            $find_ok = 'ok.ru';
            $find_vk = 'vk.com';
            $find_tg = 't.me';
            $find_inst = 'instagram.com';
            $find_facebook = 'facebook.com';
            $find_ya_dzen = 'zen.yandex.ru';
            $find_youtube = 'youtu';
            $find_tt = 'tiktok.com';
            $ok = strpos($link_to_find, $find_ok);
            $vk = strpos($link_to_find, $find_vk);
            $tg = strpos($link_to_find, $find_tg);
            $inst = strpos($link_to_find, $find_inst);
            $facebook = strpos($link_to_find, $find_facebook);
            $ya_dzen = strpos($link_to_find, $find_ya_dzen);
            $youtube = strpos($link_to_find, $find_youtube);
            $tt = strpos($link_to_find, $find_tt);
            if ($ok !== false) {
                $ok_array["status"] = "true";
                $ok_array["link"][] = $link->link;
            }
            if ($vk !== false) {
                $vk_array["status"] = "true";
                $vk_array["link"][] = $link->link;
//                dd($vk_array);
            }
            if ($tg !== false) {
                $tg_array["status"] = "true";
                $tg_array["link"][] = $link->link;
            }
            if ($inst !== false) {
                $inst_array["status"] = "true";
                $inst_array["link"][] = $link->link;
            }

            if ($facebook !== false) {
                $facebook_array["status"] = "true";
                $facebook_array["link"][] = $link->link;
            }
            if ($ya_dzen !== false) {
                $ya_dzen_array["status"] = "true";
                $ya_dzen_array["link"][] = $link->link;
            }
            if ($youtube !== false) {
                $youtube_array["status"] = "true";
                $youtube_array["link"][] = $link->link;
            }
            if ($tt !== false) {
                $tt_array["status"] = "true";
                $tt_array["link"][] = $link->link;
            }
            if (isset($tt_array["status"]) && $tt_array["status"] == "true"){
                if (isset($tt_array["active"]) && $tt_array["active"] == 0){
                    $tt_array["active"] = 1;
                    $text .= "<b>TT,</b>";
                }
            }
            if (isset($youtube_array["status"]) && $youtube_array["status"] == "true"){
                if (isset($youtube_array["active"]) && $youtube_array["active"] == 0){
                $youtube_array["active"] = 1;
                $text .= "<b>YT,</b>";
                }
            }
            if (isset($ya_dzen_array["status"]) && $ya_dzen_array["status"] == "true"){
                if (isset($ya_dzen_array["active"]) && $ya_dzen_array["active"] == 0){
                $ya_dzen_array["active"] = 1;
                $text .= "<b>Я.Д,</b>";
                }
            }
            if (isset($facebook_array["status"]) && $facebook_array["status"] == "true"){
                if (isset($facebook_array["active"]) && $facebook_array["active"] == 0) {
                    $facebook_array["active"] = 1;
                    $text .= "<b>FB,</b>";
                }
            }
            if (isset($inst_array["status"]) && $inst_array["status"] == "true"){
                if (isset($inst_array["active"]) && $inst_array["active"] == 0) {
                    $inst_array["active"] = 1;
                    $text .= "<b>INST,</b>";
                }
            }
            if (isset($tg_array["status"]) && $tg_array["status"] == "true"){
                if (isset($tg_array["active"]) && $tg_array["active"] == 0) {
                    $tg_array["active"] = 1;
                    $text .= "<b>TG,</b>";
                }
            }
            if (isset($vk_array["status"]) && $vk_array["status"] == "true"){
                if (isset($vk_array["active"]) && $vk_array["active"] == 0) {
                    $vk_array["active"] = 1;
                    $text .= "<b>VK,</b>";
                }
            }
            if (isset($ok_array["status"]) && $ok_array["status"] == "true"){
                if ($ok_array["active"] == 0) {
                    $ok_array["active"] = 1;
                    $text .= "<b>OK,</b>";
                }
            }
        }

        $text .= "\n\n<b>Ссылка на материал для посева:</b> \n";
        foreach ($tt_array["link"] as $tt_link) {
            if ($tt_array["active"] == 1) {
                $text .= '<a href="' . $tt_link . '">' . $tt_link . '</a>' . "\n";
            }
        }
        foreach ($inst_array["link"] as $inst_link) {
            if ($inst_array["active"] == 1) {
                $text .= '<a href="' . $inst_link . '">' . $inst_link . '</a>' . "\n";
            }
        }
        foreach ($ok_array["link"] as $ok_link) {
            if ($ok_array["active"] == 1) {
                $text .= '<a href="' . $ok_link . '">' . $ok_link . '</a>' . "\n";
            }
        }
        foreach ($vk_array["link"] as $vk_link) {
            if ($vk_array["active"] == 1) {
                $text .= '<a href="' . $vk_link . '">' . $vk_link . '</a>' . "\n";
            }
        }
        foreach ($tg_array["link"] as $tg_link) {
            if ($tg_array["active"] == 1) {
                $text .= '<a href="' . $tg_link . '">' . $tg_link . '</a>' . "\n";
            }
        }
        foreach ($facebook_array["link"] as $facebook_link) {
            if ($facebook_array["active"] == 1) {
                $text .= '<a href="' . $facebook_link . '">' . $facebook_link . '</a>' . "\n";
            }
        }
        foreach ($youtube_array["link"] as $youtube_link) {
            if ($youtube_array["active"] == 1) {
                $text .= '<a href="' . $youtube_link . '">' . $youtube_link . '</a>' . "\n";
            }
        }
        foreach ($ya_dzen_array["link"] as $ya_dzen_link) {
            if ($ya_dzen_array["active"] == 1) {
                $text .= '<a href="' . $ya_dzen_link . '">' . $ya_dzen_link . '</a>' . "\n";
            }
        }

        if (isset($post->seeding_text)) {
            $text .= "\n<b>Комментарий:</b>" . "\n" . $post->seeding_text . "\n";
        }
        $text .= "\n<b>Вы можете открыть задачу, перейдя по ссылке: </b>" . '<a href="/posts/' . $post->id . '">' . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . '</a>';
        $this->telegram->sendMessageToAllInOneRole($text, 'seeder');
    }

    /**
     * Отправляем уведомление всем Таргетологам, сидерам и комментаторам
     * @param Post $post
     */
    private function sendTelegramNotification(Post $post, $smmLinks)
    {

        $formattText = $this->formatPostText($post->text);
        $arr = [];
        foreach($smmLinks as $social => $links) {
            foreach($links as $key => $link) {
                $socialNetwork_name = SocialNetwork::where('slug', $social)->first()->name;
                $arr[] = $socialNetwork_name;
            }
        }

        $text = "Поставлена новая задача\n";
        $const_text = "$post->id - $post->title\n"
            . "<b>Тезисы: </b>\n"
            . $formattText . "\n"
            . "<b>Ссылка на материал: </b><a href='" . $post->draft_url . "'>" . $post->draft_url . "</a> \n"
            . "<b>Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a></b>";
        $text .= "<b>Задача размещена в " . implode(",", $arr) . ": </b>\n" . $const_text;
//        $this->telegram->sendMessageToAllInOneRole($text, 'seeder');
        $this->telegram->sendMessageToAllInOneRole($text, 'commenter');
    }

    /**
     * Удаляет пост из БД.
     *
     * @param Post $post
     * @return RedirectResponse
     * @throws Exception
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->route('posts.index');
    }

    /**
     * @param Post $post
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function copy(Post $post)
    {

        $this->authorize('update', $post);

        $time = Carbon::now()->add('4', 'hours')
            ->setTimezone(Auth::user()->timezone);

        $projects = Project::all()->filter(function ($item) {
            return $item['archived_at'] == null;
        });
        $currentProject = $projects->first();

        $journalists = User::role('journalist')->get();
        $projects = Project::all()->filter(function ($item) {
            return $item['archived_at'] == null;
        });

        $socialNetworks = SocialNetwork::get();
        $socialNetworksSeed = SocialNetwork::get();
        $selectedSocialNetworks = $post->socialNetworks->pluck('id')->toArray();
        $selectedSocialSeedNetworks = $post->seedLinks->pluck('id')->toArray();
        return view('posts.partials.copy', compact('projects', 'post', 'journalists', 'currentProject', 'time', 'socialNetworks', 'selectedSocialNetworks', 'socialNetworksSeed', 'selectedSocialSeedNetworks'));
    }


    /**
     * Обновляет статус задачи в работу
     *
     * @param Post $post
     * @param UpdateStatusPostRequest $request
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function updateStatus(Post $post, UpdateStatusPostRequest $request)
    {

        $validated = $request->validated();

        $this->authorize('set-assignee', $post);
        if ($post->journalist_id != $validated['journalist_id'] && $post->journalist_id != null) {

            return back()->with('error', 'На публикацию уже назначен исполнитель');
        }

        // если журналист пытается назначить кого-то кроме себя
        if (auth()->user()->hasRole('journalist') && auth()->id() !== (int)$validated['journalist_id']) {

            return back()->with('error', 'У вас недостаточно прав для назначения других пользователей на публикацию');
        }

        // если редактор пытается назначить сам себя в качестве исполнителя
        /*
        if (auth()->user()->hasRole('editor') && auth()->id() === (int) $validated['journalist_id']) {
            return back()->with('error', 'Вы не можете назначить себя в качестве исполнителя');
        }
        */

        $post->status_task = true;

        $post->journalist_id = $validated['journalist_id'];
        $post->status_id = 3;
        $post->save();


        $this->telegramBot->editor->NotificationEditor($post->id, 'journalist_take_task');


        return back()->with('message', 'Задача отправлена в работу и вы были назначены исполнителем');
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
