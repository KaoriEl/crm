<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Idea;
use App\Models\Temp_task;
use App\Models\User;
use App\Models\Project;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Commands\Command;
use App\Models\Cache;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Http\Controllers\BotUsers\EditorController;
use App\Http\Controllers\BotUsers\JournalistController;
use Telegram\Bot\Objects\MessageEntity;

class TelegramBotController extends Controller
{

    /** @var Api */
    protected $telegram;


    /** @var JournalistController
     * @var EditorController
     */
    public $journalist;
    public $editor;

    /**
     * Бот контроллер конструктор
     *
     * @param Api $telegram
     *
     * @return void;
     */

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
        $this->journalist = new JournalistController();
        $this->editor = new EditorController();
    }

    /**
     * Установка веб-хука
     *
     * @param null
     *
     * @return void;
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */

    public function setWebhook()
    {
        $url = 'https://ytg-edit.ru/telegram/bot/webhook'; // указываем url на который будет ссылаться бот
        $response = $this->telegram->setWebhook(['url' => $url]);
        dd($response);
    }


    /**
     * Удаление вебхука
     * @param null
     * @return void
     */
    public function removeWebhook()
    {
        $response = $this->telegram->removeWebhook();
        dd($response);
    }

    public function updatedActivity()
    {
        // TODO: Использовать после удаления вебхука
        //
        // Устарело из-за вебхука

        $activity = Telegram::getUpdates(['offset' => '147287113']);


;
        dd($activity);
        foreach ($activity as $item) {
            $username = $item['message']['from']['username'];
            $telegram_id = $item['message']['from']['id'];
            $user = User::wherePhone($username)->first();
            if(($user)&&($telegram_id)){
                $user->telegram_id = $telegram_id;
                $user->save();
            }

        }


    }


    public function sendMessage()
    {
        return view('telegram.message');
    }

    /**
     * Отправляем сообщение пользователю в заивсимости от роли
     * @param $text
     * @param $role
     * @param null $keyboard
     * @return RedirectResponse
     */
    public function sendMessageToAllInOneRole($text, $role, $keyboard = null) {
        $users = User::all();
        foreach ($users as $user) {
            if(($user->telegram_id)&&($user->hasRole($role))) {
                if(is_null($keyboard)) {
                    try {
                        Telegram::sendMessage([
                            'chat_id' => env('TELEGRAM_CHANNEL_ID', $user->telegram_id),
                            'parse_mode' => 'HTML',
                            'text' => $text,
                            'disable_web_page_preview' => true
                        ]);
                    } catch (\Exception  $e) {
                        continue;
                    }
                } else {
                    try {
                        Telegram::sendMessage([
                            'chat_id' => env('TELEGRAM_CHANNEL_ID', $user->telegram_id),
                            'parse_mode' => 'HTML',
                            'text' => $text,
                            'disable_web_page_preview' => true,
                            'reply_markup' => $keyboard
                        ]);
                    } catch (\Exception  $e) {

                    }
                }

            }
        }
        return redirect()->back();
    }

    /**
     * Отправляем сообщение одному пользователю
     * @param $telegram_id
     * @param $text
     * @param null $keyboard
     */
    public function sendMessageToOneUser($telegram_id, $text, $keyboard = null): void
    {
        if(is_null($keyboard)) {
            try {
                Telegram::sendMessage([
                    'chat_id' => $telegram_id,
                    'parse_mode' => 'HTML',
                    'text' => $text,
                    'disable_web_page_preview' => true
                ]);
            } catch (\Exception  $e) {

            }
        } else {
            try {
                Telegram::sendMessage([
                    'chat_id' => $telegram_id,
                    'parse_mode' => 'HTML',
                    'text' => $text,
                    'disable_web_page_preview' => true,
                    'reply_markup' => $keyboard
                ]);
            } catch (\Exception  $e) {

            }
        }

    }

    /**
     * Отправляет задачу и уведомление о назначении журналисту/журналистам.
     * @param Post $post
     * @return RedirectResponse
     */
    public function storeMessage(Post $post)
    {
        $formattText = $this->formatPostText($post->text);
        $deadline_post = $post->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';
        if ($post->hasJournalist() && $post->journalist->telegram_id) {
            $posts = Post::all()->where('archived_at', null)->where('journalist_id', $post->journalist->id)->where('publication_url',null)->count();
            $keyboard = Keyboard::make()
                ->inline()
                ->row(
                    Keyboard::inlineButton(['text' => 'Показать список назначенных задач (' . $posts . ')', 'callback_data' => 'take_tasks']));
            $keyboard->row(
                Keyboard::inlineButton(['text' => 'Взять задачу в работу' , 'callback_data' => 'take_in_work_'.$post->id]));
            $nameJournalist = $post->journalist->name;
            $text = "Назначена новая задача\n"
                . "<b>Вы были назначены ответственным по задаче: </b>\n"
                . "$post->id - $post->title\n"
                . "<b>Исполнитель: $nameJournalist </b>\n"
                . "<b>Тезисы: </b>\n"
                . $formattText . "\n"
                . "Дедлайн: " . $deadline_post . "\n"
                . "<b>Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a></b>";
            $telegram_id = $post->journalist->telegram_id;
            $this->sendMessageToOneUser($telegram_id, $text, $keyboard);
        } else {
            $text = "Назначена новая задача\n"
                . "<b>Создана задача без ответственного </b>\n"
                . "$post->id - $post->title\n"
                . "<b>Тезисы: </b>\n"
                . $formattText . "\n"
                . "Дедлайн: " . $deadline_post . "\n"
                . "<b>Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a></b>";
            $users = User::all();
            foreach ($users as $user) {
                $arProject = [];
                foreach ($user->projects as $project) {
                    $arProject[] = $project->id;
                }
                if (($user->telegram_id) && ($user->hasRole('journalist')) && (in_array($post->project_id, $arProject))) {
                    $posts = Post::all()->where('archived_at', null)->where('journalist_id', $user->id)->where('publication_url',null)->count();
                    $keyboard = Keyboard::make()
                        ->inline();
                    $keyboard->row(
                        Keyboard::inlineButton(['text' => 'Показать список назначенных задач (' . $posts . ')' , 'callback_data' => 'take_tasks']));
                    $keyboard->row(
                        Keyboard::inlineButton(['text' => 'Взять задачу в работу' , 'callback_data' => 'take_in_work_'.$post->id]));
                    $this->sendMessageToOneUser($user->telegram_id, $text, $keyboard);
                }
            }

        }

        return redirect()->back();
    }

    /**
     * Уведомление редактору если приняли его материал
     * @param $post
     * @return RedirectResponse
     */
    public function storeMessageModerateApprove($post)
    {
        $formattText = $this->formatPostText($post->text);
        $deadline_post = $post->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';
        $posts = Post::all()->where('archived_at',null)->where('journalist_id',$post->journalist->id)->where('publication_url',null)->count();
        $keyboard = Keyboard::make()
            ->inline()
            ->row(
                Keyboard::inlineButton(['text' => 'Показать список назначенных задач (' .$posts . ')' , 'callback_data' => 'take_tasks']))
            ->row(
                Keyboard::inlineButton(['text' => 'Отправить ссылку на публикацию' , 'callback_data' => 'give_publication_url_'.$post->id]));

        $text = "<b>Материал принят </b> \n"
            . "$post->id - $post->title\n"
            . "<b>Тезисы: </b>\n"
            . $formattText . "\n"
            . "<b>Дедлайн:</b> " . $deadline_post . "\n"
            . "<b>Ссылка на материал: </b><a href='" . $post->draft_url . "'>" . $post->draft_url . "</a> \n"
            . "<b>Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a></b>";

        if ($post->hasJournalist() && $post->journalist->telegram_id) {
            $this->sendMessageToOneUser($post->journalist->telegram_id, $text, $keyboard);
        }else{
            $this->sendMessageToAllInOneRole($text, 'journalist', $keyboard);
        }
        return redirect()->back();
    }

    /**
     * Отправка уведомления журналисту, если его публикацию не приняли
     * @param $post
     * @return RedirectResponse
     */
    public function storeMessageModerateRework($post)
    {
        $formattText = $this->formatPostText($post->text);
        $posts = Post::all()->where('archived_at',null)->where('journalist_id',$post->journalist->id)->where('publication_url',null)->count();
        $keyboard = Keyboard::make()
            ->inline()
            ->row(
                Keyboard::inlineButton(['text' => 'Показать список назначенных задач (' .$posts . ')', 'callback_data' => 'take_tasks']));

        $keyboard->row(
            Keyboard::inlineButton(['text' => 'Отправить ссылку на материал' , 'callback_data' => 'give_draft_url_'.$post->id]));
        $comments = '';
        $array_comments = array_reverse($post->comments->toArray());
        foreach($array_comments as $value) {
            if($value['role'] == 'Журналист') {
                $comments .= "<b>Комментарий журналиста: </b>" . $value['text'] . "\n";
            } else {
                $comments .= "<b>Комментарий редактора: </b>" . $value['text'] . "\n";
            }
        }

        $comments = $this->formatPostText($comments);
        $deadline_post = $post->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';

        $text = "<b>Публикация отправлена на доработку</b> \n"
            . "$post->id - $post->title\n"
            . "<b>Тезисы: </b>\n"
            . $formattText . "\n"
            . "Дедлайн: " . $deadline_post . "\n"
            . $comments
            . "<b>Ссылка на материал: </b><a href='" . $post->draft_url . "'>" . $post->draft_url . "</a> \n"
            . "<b>Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a></b>";

        if ($post->hasJournalist() && $post->journalist->telegram_id) {
            $telegram_id = $post->journalist->telegram_id;
            $cache_user = Cache::all()->where('telegram_id', $telegram_id)->first();
            $cache_user->forward_step = 'give_draft_link';
            $cache_user->current_step = 'wait_draft_link';
            $cache_user->save();
            $this->sendMessageToOneUser($telegram_id, $text, $keyboard);
        }else{
            $text = "<b>Публикация отправлена на доработку</b> \n"
                . "$post->id - $post->title\n"
                . "<b>Тезисы: </b>\n"
                . $formattText . "\n"
                . "Дедлайн: " . $deadline_post . "\n"
                . "<b>Ссылка на материал: </b><a href='" . $post->draft_url . "'>" . $post->draft_url . "</a> \n"
                . "<b>Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a></b>";
            $this->sendMessageToAllInOneRole($text, 'journalist', $keyboard);
        }
        return redirect()->back();
    }

    /**
     * Отправляет уведомление журналисту, если на него назначили задачу
     * @param $post
     */
    public function sendMessageChangedJournalist($post)
    {
        $formattText = $this->formatPostText($post->text);

        $posts = Post::all()->where('archived_at', null)->where('journalist_id', $post->journalist->id)->where('publication_url',null)->count();
        $keyboard = Keyboard::make()
            ->inline()
            ->row(
                Keyboard::inlineButton(['text' => 'Показать список назначенных задач (' . $posts . ')', 'callback_data' => 'take_tasks']));

        $text = "Вы были назначены ответственным по задаче\n"
            . "$post->id - $post->title\n"
            . "<b>Тезисы: </b>\n"
            . $formattText . "\n"
            . "<b>Ссылка на материал: </b><a href='" . $post->draft_url . "'>" . $post->draft_url . "</a> \n"
            . "<b>Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a></b>";

        if ($post->hasJournalist() && $post->journalist->telegram_id) {
            $this->sendMessageToOneUser($post->journalist->telegram_id, $text, $keyboard);
        }
    }

    public function sendPhoto()
    {
        return view('telegram.photo');
    }

    public function storePhoto(Request $request)
    {
        $request->validate([
            'file' => 'file|mimes:jpeg,png,gif'
        ]);

        $photo = $request->file('file');

        Telegram::sendPhoto([
            'chat_id' => env('TELEGRAM_CHANNEL_ID', '137395252'),
            'photo' => InputFile::createFromContents(file_get_contents($photo->getRealPath()), str_random(10) . '.' . $photo->getClientOriginalExtension())
        ]);

        return redirect()->back();
        //
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

    public function expiresAt($time)
    {
        // переведем дату из часового пояса пользователя в часовой пояс приложения (UTC по-умолчанию)
        return Carbon::createFromFormat('Y-m-d H:i', $time)->format('Y-m-d H:i');
    }
}
