<?php


namespace App\Pipes\Posts;


use App\Http\Controllers\TelegramBotController;
use App\Http\Controllers\TelegramBotController as TelegramBot;
use App\Models\Post;
use Auth;
use Closure;
use Telegram\Bot\Api;

class UpdateSeedingPipe
{


    private $telegramBot;
    private $telegram;

    /**
     * Создает новый экземпляр Пайпа
     *
     * @param Api $telegram
     */
    public function __construct(Api $telegram)
    {
        $this->telegramBot = new TelegramBot($telegram);
        $this->telegram = new TelegramBotController(new Api());
    }

    /**
     * Переводит первый символ строки в верхний регистр
     *
     * @param Post $post
     * @param Closure $next
     * @return mixed
     */
    public function handle($pipeArgs, Closure $next)
    {
        $post = $pipeArgs['post'];
        $arrSMMLinks = $pipeArgs['arrSMMLinks'];
        $arrSeedLinks = $pipeArgs['arrSeedLinks'];
        $validated = $pipeArgs['validated'];
        $user = Auth::user();
        if ($post->seeding && $user->hasRole('seeder') && is_null($post->seeder_id) && isset($validated['seed_list_url'])) {
            $post->seeder_id = $user->id;
            $post->save();

            $this->telegramBot->editor->NotificationEditor($post->id, "seeder_send_link");
        }


        if ($post->commercial_seed && $user->hasRole('seeder') && isset($validated['seed_links'])) {
            $this->telegramBot->editor->NotificationEditor($post->id, "seeder_commercial_send_link");
        }


        return $next($pipeArgs);
    }
}
