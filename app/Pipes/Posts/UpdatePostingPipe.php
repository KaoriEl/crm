<?php


namespace App\Pipes\Posts;


use App\Http\Controllers\Excel\WorkWithExcel;
use App\Http\Controllers\TelegramBotController;
use App\Http\Controllers\TelegramBotController as TelegramBot;
use App\Models\Post;
use App\Models\User;
use Auth;
use Closure;
use Telegram\Bot\Api;

class UpdatePostingPipe
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
     * @param $pipeArgs
     * @param Closure $next
     * @return mixed
     */
    public function handle($pipeArgs, Closure $next)
    {
        $post = $pipeArgs['post'];
        $arrSMMLinks = $pipeArgs['arrSMMLinks'];
        $arrSeedLinks = $pipeArgs['arrSeedLinks'];
        $user = Auth::user();
        if ($post->posting && $user->hasRole('smm')) {
            if (file_exists(base_path() . '/public_html/credentials.json')) {
                $excel = new WorkWithExcel();
            }

            $task_for_smm = [];

            if ($post->posting_to_vk) {
                if(isset($arrSMMLinks) && isset($arrSMMLinks['vk']) && file_exists(base_path() . '/public_html/credentials.json')) {
                    foreach ($arrSMMLinks['vk'] as $link) {
                        $excel->addingLinkInExcel($post, $link);
                    }
                }
            }

            if ($post->posting_to_ig) {
                if(isset($arrSMMLinks) && isset($arrSMMLinks['ig']) && file_exists(base_path() . '/public_html/credentials.json')) {
                    foreach ($arrSMMLinks['ig'] as $link) {
                        $excel->addingLinkInExcel($post, $link);
                    }
                }
            }

            if ($post->posting_to_tg) {
                if(isset($arrSMMLinks) && isset($arrSMMLinks['tg']) && file_exists(base_path() . '/public_html/credentials.json')) {
                    foreach ($arrSMMLinks['tg'] as $link) {
                        $excel->addingLinkInExcel($post, $link);
                    }
                }
            }

            $post->smm_id = $user->id;
            $post->save();
            $this->telegramBot->editor->NotificationEditor($post->id, "smm_send_links");
        }

        return $next($pipeArgs);
    }
}
