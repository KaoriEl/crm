<?php


namespace App\Pipes\Posts;


use App\Http\Controllers\TelegramBotController;
use App\Http\Controllers\TelegramBotController as TelegramBot;
use App\Models\Post;
use Auth;
use Closure;
use Telegram\Bot\Api;

class UpdateCommentPipe
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
        $user = Auth::user();

        if ($post->commenting && $user->hasRole('commenter') && $post->posting) {

            $comments_count = 0;
            if ($link = request()->get('default_screenshot_url')) {
                $post->commented = 1;
                $post->default_screenshot_url = $link;
                $comments_count++;
            }

            if ($link = request()->get('vk_screenshot')) {
                $post->commented = 1;
                $post->vk_screenshot = $link;
                $comments_count++;
            }


            if ($link = request()->get('ok_screenshot')) {
                $post->commented = 1;
                $post->ok_screenshot = $link;
                $comments_count++;
            }


            if ($link = request()->get('fb_screenshot')) {
                $post->commented = 1;
                $post->fb_screenshot = $link;
                $comments_count++;
            }


            if ($link = request()->get('ig_screenshot')) {
                $post->commented = 1;
                $post->ig_screenshot = $link;
                $comments_count++;
            }

            if ($link = request()->get('y_dzen_screenshot')) {
                $post->commented = 1;
                $post->y_dzen_screenshot = $link;
                $comments_count++;
            }

            if ($link = request()->get('y_street_screenshot')) {
                $post->commented = 1;
                $post->y_street_screenshot = $link;
                $comments_count++;
            }

            if ($link = request()->get('yt_screenshot')) {
                $post->commented = 1;
                $post->yt_screenshot = $link;
                $comments_count++;
            }

            if ($link = request()->get('tg_screenshot')) {
                $post->commented = 1;
                $post->tg_screenshot = $link;
                $comments_count++;
            }


            $task_for_commentator = [];
            ($post->default_screenshot_url) ? $task_for_commentator[] = 'default' : '';
            ($post->vk_screenshot) ? $task_for_commentator[] = 'vk' : '';
            ($post->ok_screenshot) ? $task_for_commentator[] = 'ok' : '';
            ($post->fb_screenshot) ? $task_for_commentator[] = 'fb' : '';
            ($post->ig_screenshot) ? $task_for_commentator[] = 'insta' : '';
            ($post->tg_screenshot) ? $task_for_commentator[] = 'tg' : '';
            ($post->yt_screenshot) ? $task_for_commentator[] = 'yt' : '';
            ($post->y_street_screenshot) ? $task_for_commentator[] = 'y_street' : '';
            ($post->y_dzen_screenshot) ? $task_for_commentator[] = 'y_dzen' : '';

            $comments = "<b>Комменатарии: </b>\n";
            foreach ($task_for_commentator as $value) {
                if ($comments_count == 0) {
                    return back()->with('message', 'Информация успешно обновлена');
                } else {
                    $comments .= ($value == 'default') ? 'Комментарий к задаче добавлен - <a href="' . $post->default_screenshot_url . '\">' . $post->default_screenshot_url . '</a>' : '';
                    $comments .= ($value == 'vk') ? 'Комментарий в ВК добавлен - <a href="' . $post->vk_screenshot . '\">' . $post->vk_screenshot . '</a>' : '';
                    $comments .= ($value == 'ok') ? 'Комментарий в OK добавлен - <a href="' . $post->ok_screenshot . '\">' . $post->ok_screenshot . '</a>' : '';
                    $comments .= ($value == 'fb') ? 'Комментарий в FB добавлен - <a href="' . $post->fb_screenshot . '\">' . $post->fb_screenshot . '</a>' : '';
                    $comments .= ($value == 'insta') ? 'Комментарий в INSTA добавлен - <a href="' . $post->ig_screenshot . '\">' . $post->ig_screenshot . '</a>' : '';
                    $comments .= ($value == 'tg') ? 'Комментарий в TG добавлен - <a href="' . $post->tg_screenshot . '\">' . $post->tg_screenshot . '</a>' : '';
                    $comments .= ($value == 'yt') ? 'Комментарий в YT добавлен - <a href="' . $post->yt_screenshot . '\">' . $post->yt_screenshot . '</a>' : '';
                    $comments .= ($value == 'y_street') ? 'Комментарий в Я.Районы добавлен - <a href="' . $post->y_street_screenshot . '\">' . $post->y_street_screenshot . '</a>' : '';
                    $comments .= ($value == 'y_dzen') ? 'Комментарий в Я.Дзен добавлен - <a href="' . $post->y_dzen_screenshot . '\">' . $post->y_dzen_screenshot . '</a>' : '';
                    $comments .= "\n";
                }
            }

            if ($comments_count > 0) {
                $this->telegramBot->editor->NotificationEditor($post->id, 'commenter_send_comments', $comments);
            }

            $post->commentator_id = $user->id;
            $post->save();
        }

        return $next($pipeArgs);
    }
}
