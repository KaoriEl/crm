<?php


namespace App\Http\Controllers\Telegram\Traits;


use App\Models\Post;
use Telegram\Bot\Keyboard\Keyboard;

class GetStatuses
{
    /**
     * Получение статусов задач
     * @param string $role
     * @param int $user_id
     * @return Keyboard
     */
    public function getStatuses(string $role, int $user_id) {
        switch($role) {
            case '4':
                return $this->editorStatusesTasks($user_id);
                break;
            case '5':
                return $this->journalistStatusesTasks($user_id);
                break;
        }

    }

    /**
     * Получение статусов задач для главного редактора
     * @param $user_id
     * @return Keyboard
     */
    public function editorStatusesTasks($user_id) {
        $keyboard = Keyboard::make()
            ->inline();

        $status = ['not_journalist' => Post::get()->where('archived_at', null)->where('editor_id', $user_id)->where('status_id', 2)->count(),
                    'not_work' => Post::get()->where('archived_at', null)->where('editor_id', $user_id)->where('status_id', 1)->count(),
                    'in_work' => Post::get()->where('archived_at', null)->where('editor_id', $user_id)->where('status_id', 3)->count(),
                    'wait_check' => Post::get()->where('archived_at', null)->where('editor_id', $user_id)->where('status_id', 4)->count(),
                    'wait_public' => Post::get()->where('archived_at', null)->where('editor_id', $user_id)->where('status_id', 6)->count(),
                    'wait_posting_to_social' => Post::get()->where('archived_at', null)->where('archived_at', null)->whereNotNull('publication_url')->where('posting', 1)->where('smm_id', 0)->where('editor_id', $user_id)->count(),
                    'wait_target_to_social' => Post::get()->where('archived_at', null)->where('archived_at', null)->where('posting', 1)->where('smm_id', '>', 0)->where('targeting', 1)->where('targeter_id', 0)->where('editor_id', $user_id)->count(),
                    'wait_seed' => Post::get()->where('archived_at', null)->where('archived_at', null)->where('posting', 1)->where('smm_id', '>', 0)->where('seeding', 1)->where('seeder_id', 0)->where('editor_id', $user_id)->count(),
                    'wait_comment' => Post::get()->where('archived_at', null)->where('archived_at', null)->where('posting', 1)->where('smm_id', '>', 0)->where('commenting', 1)->where('commentator_id', 0)->where('editor_id', $user_id)->count()
            ];

        ($status['not_journalist']) ?  $keyboard->row(Keyboard::inlineButton(['text' => 'Задачи без назначения (' . $status['not_journalist'] . ')  ', 'callback_data' => 'status_not_journalist',])
        ) : false;
        ($status['not_work']) ?  $keyboard->row(Keyboard::inlineButton(['text' => 'Задачи не в работе (' . $status['not_work'] . ')  ', 'callback_data' => 'status_not_work',])
        ) : false;
        ($status['in_work']) ?  $keyboard->row(Keyboard::inlineButton(['text' => 'Задачи в работе (' . $status['in_work'] . ')  ', 'callback_data' => 'status_in_work',])
        ) : false;
        ($status['wait_check']) ?  $keyboard->row(Keyboard::inlineButton(['text' => 'Требуют проверки (' . $status['wait_check'] . ')  ', 'callback_data' => 'status_wait_check',])
        ) : false;
        ($status['wait_public']) ?  $keyboard->row(Keyboard::inlineButton(['text' => 'Ожидает публикации (' . $status['wait_public'] . ')  ', 'callback_data' => 'status_wait_public',])
        ) : false;
        ($status['wait_posting_to_social']) ?  $keyboard->row(Keyboard::inlineButton(['text' => 'Ожидают размещение в соц сетях (' . $status['wait_posting_to_social'] . ')', 'callback_data' => 'status_posting_social'])
        ) : false;
        ($status['wait_target_to_social']) ?  $keyboard->row(Keyboard::inlineButton(['text' => 'Ожидают таргетированную рекламу (' . $status['wait_target_to_social'] . ')', 'callback_data' => 'status_target_social'])
        ) : false;
        ($status['wait_seed']) ?  $keyboard->row(Keyboard::inlineButton(['text' => 'Ожидают посев (' . $status['wait_seed'] . ')', 'callback_data' => 'status_wait_seed'])
        ) : false;
        ($status['wait_comment']) ?  $keyboard->row(Keyboard::inlineButton(['text' => 'Ожидают комментирование (' . $status['wait_comment'] . ')', 'callback_data' => 'status_wait_comment'])
        ) : false;
        return $keyboard;
    }

    /**
     * Получение статусов задач для журналиста
     * @param $user_id
     * @return Keyboard
     */
    public function journalistStatusesTasks($user_id) {
        $keyboard = Keyboard::make()
            ->inline();

        $status = ['not_journalist' => Post::get()->where('archived_at', null)->where('journalist_id', null)->where('status_id', 2)->count(),
                    'not_work' => Post::get()->where('archived_at', null)->where('journalist_id', $user_id)->where('status_id', 1)->count(),
                    'in_work' => Post::get()->where('archived_at', null)->where('journalist_id', $user_id)->where('status_id', 3)->count(),
                    'wait_check' => Post::get()->where('archived_at', null)->where('journalist_id', $user_id)->where('status_id', 4)->count(),
                    'in_completion' => Post::get()->where('archived_at', null)->where('journalist_id', $user_id)->where('status_id', 5)->count(),
                    'wait_public' => Post::get()->where('archived_at', null)->where('journalist_id', $user_id)->where('status_id', 6)->count() ];

        ($status['not_journalist']) ?  $keyboard->row(Keyboard::inlineButton(['text' => 'Без назначения (' . $status['not_journalist'] . ')  ', 'callback_data' => 'status_not_journalist',])
        ) : false;

        ($status['not_work']) ?  $keyboard->row(Keyboard::inlineButton(['text' => 'Не в работе (' . $status['not_work'] . ')  ', 'callback_data' => 'status_not_work'])
        ) : false;

        ($status['in_work']) ?  $keyboard->row(Keyboard::inlineButton(['text' => 'В работе ('  . $status['in_work'] . ')', 'callback_data' => 'status_in_work'])
        ) : false;

        ($status['wait_check']) ?  $keyboard->row(Keyboard::inlineButton(['text' => 'Нужна проверка (' . $status['wait_check'] . ')' , 'callback_data' => 'status_wait_check'])
        ) : false;

        ($status['in_completion']) ?  $keyboard->row(Keyboard::inlineButton(['text' => 'На доработке (' . $status['in_completion'] . ')', 'callback_data' => 'status_in_completion'])
        ) : false;

        ($status['wait_public']) ?  $keyboard->row(Keyboard::inlineButton(['text' => 'Ждет публикации (' . $status['wait_public'] . ')' , 'callback_data' => 'status_wait_public'])
        ) : false;


        return $keyboard;
    }

}
