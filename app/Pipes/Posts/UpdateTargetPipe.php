<?php


namespace App\Pipes\Posts;


use App\Enums\PostTargetStatusesEnum;
use App\Http\Controllers\TelegramBotController;
use App\Http\Controllers\TelegramBotController as TelegramBot;
use App\Models\Post;
use Auth;
use Closure;
use Telegram\Bot\Api;

class UpdateTargetPipe
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
     * @param $arrSMMLinks
     * @param Closure $next
     * @return mixed
     */
    public function handle($pipeArgs, Closure $next)
    {
        $post = $pipeArgs['post'];
        $arrSMMLinks = $pipeArgs['arrSMMLinks'];
        $arrSeedLinks = $pipeArgs['arrSeedLinks'];
        $user = Auth::user();
        $post->socialNetworks()->syncWithoutDetaching(request()->input('targeted_to', []));

        if ($post->targeting && $user->hasRole('targeter') && is_null($post->target_id)) {
            $post->load('socialNetworks');

            foreach ($post->socialNetworks as $socialNetworks) {
                if ($socialNetworks->pivot->status !== PostTargetStatusesEnum::SUCCESSFUL_MODERATED_STATUS) {
                    return back()->with('message', 'Информация успешно обновлена');
                }
            }

            $post->target_id = $user->id;
            $post->save();

            $this->telegramBot->editor->NotificationEditor($post->id, "target_in_moderation");
        }

        return $next($pipeArgs);
    }
}
