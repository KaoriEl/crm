<?php

namespace App\Console\Commands;

use App\Models\Cache;
use App\Models\Telegram_user;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use App\Models\Temp_task;
use App\Models\Post;
use Illuminate\Support\Str;
use Telegram\Bot\Laravel\Facades\Telegram;

class CheckTimeoutTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:task:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверяет прошло ли более 2-ух минут с момента создании задачи';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws Exception
     */
    public function handle()
    {
        //
        $tasks = Temp_task::all();
        foreach($tasks as $temp_task) {
            $cache_user = Telegram_user::all()->where('telegram_id',$temp_task->telegram_id)->first();
            $time_update = $temp_task->updated_at;
            $time_update = strtotime($time_update);
            $time_post = strtotime("+2 minutes", $time_update);
            $time_now = strtotime('now');
            if($time_now > $time_post && $temp_task->project_id != null && $temp_task->title != null && $temp_task->expires_at != $temp_task->created_at) {
                $post = new Post();
                $post->fill($temp_task->toArray());
                $post->save();
                $cache_user->forward_step = $cache_user->current_step;
                $cache_user->post_id = null;
                $cache_user->current_step = null;
                $cache_user->save();
                $info_new_task = '';
                $formattText = $this->formatPostText($temp_task->text);
                (!is_null($temp_task->journalist_id)) ? $user_journalist = User::find($temp_task->journalist_id)->name :  $user_journalist = 'Без назначения';
                $info_new_task .= 'Задача <b>'. $temp_task->title.'</b> создана!' . "\n"
                    . '<b>Тезисы:  </b>' . "\n"
                    . $formattText . "\n"
                    . '<b>Исполнитель: </b>' .  $user_journalist . "\n";

                if($temp_task->posting) {
                    $info_new_task  .= '<b>Соц-сети: </b>';
                    ($temp_task->posting_to_vk) ? $info_new_task .= 'ВК, ' : '';
                    ($temp_task->posting_to_ok) ? $info_new_task .= 'OK, ' : '';
                    ($temp_task->posting_to_fb) ? $info_new_task .= 'FB, ' : '';
                    ($temp_task->posting_to_ig) ? $info_new_task .= 'IG, ' : '';
                    ($temp_task->posting_to_y_dzen) ? $info_new_task .= 'Я.Дзен, ' : '';
                    ($temp_task->posting_to_y_street) ? $info_new_task .= 'Я.Районы, ' : '';
                    ($temp_task->posting_to_yt) ? $info_new_task .= 'YT, ' : '';
                    ($temp_task->posting_to_tt) ? $info_new_task .= 'TT, ' : '';
                    ($temp_task->posting_to_tg) ? $info_new_task .= 'TG, ' : '';

                    $info_new_task = Str::replaceLast(', ','',$info_new_task);
                    $info_new_task .= "\n";


                    if($temp_task->targeting) {
                        $info_new_task  .= "<b>Тарегтированная реклама:</b> Да \n";

                    }

                    if($temp_task->seeding) {
                        $info_new_task  .= "<b>Посевы:</b> Да \n";
                    }

                    if($temp_task->commenting) {
                        $info_new_task  .= "<b>Комментирование:</b> Да  \n";

                    }

                }

                $info_new_task  .= "<b>Вы можете открыть задачу, перейдя по ссылке: <a href='/posts/" . $post->id . "'>" . env('APP_URL', 'http://edit.marketica-dev.ru') . "/posts/" . $post->id . "</a></b> \n";

                $temp_task->delete();
                Telegram::sendMessage([
                    'chat_id' => $temp_task->telegram_id,
                    'parse_mode' => 'HTML',
                    'text' => $info_new_task
                ]);
                app('App\Http\Controllers\TelegramBotController')->storeMessage($post);
            }

        }

    }

    private function formatPostText($text)
    {
        $formattText = strip_tags($text, '<b></b><strong></strong><i></i><em></em><a></a><code></code><pre></pre>');
        $formattText = str_replace('</p>', '\n', $formattText);
        $formattText = str_replace('<p>', '', $formattText);

        //
        return $formattText;
    }
}
