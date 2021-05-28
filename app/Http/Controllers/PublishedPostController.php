<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Excel\WorkWithExcel;
use App\Http\Requests\Post\UpdatePostPublishedRequest;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Telegram\Bot\Api as Api;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Http\Controllers\TelegramBotController as TelegramBot;
use function Amp\File\put;


class PublishedPostController extends Controller
{
    protected $telegram;
    protected $telegramBot;
    public function __construct()
    {
        $this->middleware('auth');
        $this->telegram = new TelegramBotController(new Api());
        $this->telegramBot = new TelegramBot(new Api());
    }

    /**
     * Добавляет ссылку на публикацию к статье.
     *
     * @param Post $post
     * @param UpdatePostPublishedRequest $request
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function store(Post $post, UpdatePostPublishedRequest $request)
    {
        $this->authorize('set-publication', $post);

        $validated = $request->validated();

//         TODO: убрать после доработки уведомлений, устарело
//        if($post->targeting){
//            app('App\Http\Controllers\TelegramBotController')->storeMessageTarget($post);
//        }
//        if($post->seeding){
//            app('App\Http\Controllers\TelegramBotController')->storeMessageSeed($post);
//        }
//        if($post->commenting){
//            app('App\Http\Controllers\TelegramBotController')->storeMessageComment($post);
//        }
        $post->publication_url = $validated['publication_url'];
        $post->publication_url_updated_at = Carbon::now(config('app.timezone'))->format('Y-m-d H:i:s');
//        if(stripos($validated['publication_url'], 't.me') !== false) {
//            $post->tg_post_url = $validated['publication_url'];
//            (new WorkWithExcel())->addingLinkInExcel($post, $validated['publication_url']);
//        }
//
//        if(stripos($validated['publication_url'], 'instagram.com') !== false) {
//            $post->ig_post_url = $validated['publication_url'];
//            (new WorkWithExcel())->addingLinkInExcel($post, $validated['publication_url']);
//        }

        $post->status_id = 7;
        $post->save();


        if($post->posting){
            $formattText = $this->formatPostText($post->text);
            $text = "<b>Создана задача : </b>\n"
                . "$post->id - $post->title\n"
                . "<b>Тезисы: </b>\n"
                . $formattText . "\n"
                . "<b>Ссылка на публикацию: </b><a href='" . $post->publication_url . "'>" . $post->publication_url . "</a> \n"
                . "<b>Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a></b>";
            $this->telegram->sendMessageToAllInOneRole($text, 'smm');
            $this->telegramBot->editor->NotificationEditor($post->id, 'journalist_give_publication_url');
        }

        return back()->with('message', 'Ссылка на публикацию успешно добавлена.');
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
