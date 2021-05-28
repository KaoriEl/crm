<?php


namespace App\Http\Controllers\Telegram\Traits;


use App\Models\Post;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\MessageEntity;
use TelegramBot\InlineKeyboardPagination\InlineKeyboardPagination;
use Illuminate\Support\Facades\Log;

trait GetTasksByStatus
{
    // нельзя константы какого хуя
    private $role_editor = 4;
    private $role_journalist = 5;

    // статусы, которые проверяются
    private $statuses = ['status_not_journalist', 'status_not_work', 'status_in_work', 'status_wait_check',
        'status_in_completion', 'status_wait_public', 'status_posting_social',
        'status_target_social', 'status_wait_seed', 'status_wait_comment' ];


    // Все посты
    private $posts = [];

    // количество постов, которые нужно отобразить
    private $count_posts = 0;

    // номер задачи в списке (ну типа если страница 4,ая то 27 задача)
    private $count = 0;

    // каунт по id задачи с которой нужно брать задачи
    private $from = 0;

    /**
     * Получение статусов задач
     * @param $status
     * @param $user
     * @param $role_id
     * @param null $current_page
     * @return array|string|void
     * @throws \TelegramBot\InlineKeyboardPagination\Exceptions\InlineKeyboardPaginationException
     */
    public function getTasks($status, $user, $role_id, $current_page = null) {
        switch($role_id) {
            case '4':
                return $this->getTasksEditor($status, $user, $current_page);
                break;
            case '5':
                return $this->getTasksJournalist($status, $user, $current_page);
                break;
        }
    }



    /**
     * Задачи с пагинацией редактора
     * @param $status
     * @param $current_page // Если наш пользователь просматривает список задач на определенной странице, а не на первой, то код будет работать в зависимости от этой переменной
     * @param $user
     * @return array|string
     * @throws \TelegramBot\InlineKeyboardPagination\Exceptions\InlineKeyboardPaginationException
     */
    private function getTasksEditor($status, $user, $current_page = null) {
        $tasks_with_pagination = [];
        $pagination = [];
        $text = '';
        $all_count_posts = 0;
        switch($status) {
            case $this->statuses[0]:
               $this->getPostsCounts($current_page, $user->id, 2, $this->role_editor);
                $text = "<b>Без назначения: </b> \n";
                $all_count_posts = Post::where('archived_at', null)->where('status_id', 2)->where('editor_id', $user->id)->orderBy('expires_at', 'ASC')->count();
                break;
            case $this->statuses[1]:
                $this->getPostsCounts($current_page, $user->id, 1, $this->role_editor);
                $text = "<b>Не в работе: </b> \n";
                $all_count_posts = Post::where('archived_at', null)->where('status_id', 1)->where('editor_id', $user->id)->orderBy('expires_at', 'ASC')->count();
                break;
            case $this->statuses[2]:
                $this->getPostsCounts($current_page, $user->id, 3, $this->role_editor);
                $text = "<b>В работе: </b> \n";
                $all_count_posts = Post::where('archived_at', null)->where('status_id', 3)->where('editor_id', $user->id)->orderBy('expires_at', 'ASC')->count();
                break;
            case $this->statuses[3]:
                $this->getPostsCounts($current_page, $user->id, 4, $this->role_editor);
                $text = "<b>Требуют проверки: </b> \n";
                $all_count_posts = Post::where('archived_at', null)->where('status_id', 4)->where('editor_id', $user->id)->orderBy('expires_at', 'ASC')->count();
                break;
            case $this->statuses[4]:
                $this->getPostsCounts($current_page, $user->id, 5, $this->role_editor);
                $text = "<b>На доработке: </b> \n";
                $all_count_posts = Post::where('archived_at', null)->where('status_id', 5)->where('editor_id', $user->id)->orderBy('expires_at', 'ASC')->count();
                break;
            case $this->statuses[5]:
                $this->getPostsCounts($current_page, $user->id, 6, $this->role_editor);
                $text = "<b>Ожидает публикации: </b> \n";
                $all_count_posts = Post::where('archived_at', null)->where('status_id', 6)->where('editor_id', $user->id)->orderBy('expires_at', 'ASC')->count();
                break;
            case $this->statuses[6]:
                // Такие статусы не расчитать из-за архитектуры (сммщик, таргетер и посевщик могут работать одновременно)))))))
                if($current_page && $current_page != 1) {
                    $this->posts = Post::whereNull('archived_at')->where('status_id', 7)->where('posting', 1)->where('smm_id', null)->where('editor_id', $user->id)->orderBy('expires_at', 'ASC')->paginate(($current_page + 1) * 5);
                    if(5 * $current_page < $this->posts->count())
                        $this->from = $this->posts->count() - 5;
                    else
                        $this->from = ($current_page - 1) * 5;

                    $this->count =  (($current_page * 5) - 5) + 1;
                    $this->count_posts = $this->posts->count();

                } else {
                    $this->posts = Post::whereNull('archived_at')->where('status_id', 7)->where('posting', 1)->where('smm_id', null)->where('editor_id', $user->id)->orderBy('expires_at', 'ASC')->paginate(5);
                    $this->from = 0;
                    $this->count_posts = $this->posts->count();
                    $this->count = 1;
                }

                $text = "<b>Ожидает размещения в соц-сетях: </b> \n";
                $all_count_posts = Post::get()->where('archived_at', null)->where('status_id', 7)->where('posting', 1)->where('smm_id', null)->where('editor_id', $user->id)->count();
                break;
            case $this->statuses[7]:

                if($current_page && $current_page != 1) {
                    $this->posts = Post::where('archived_at', null)->where('posting', 1)->where('smm_id', '!=', null)->where('targeting', 1)->where('editor_id', $user->id)->where('target_id', null)->orderBy('expires_at', 'ASC')->paginate(($current_page + 1) * 5);
                    if(5 * $current_page < $this->posts->count())
                        $this->from = $this->posts->count() - 5;
                    else
                        $this->from = ($current_page - 1) * 5;

                    $this->count_posts = $this->posts->count();
                    $this->count = (($current_page * 5) - 5) + 1;

                } else {
                    $this->posts = Post::where('archived_at', null)->where('posting', 1)->where('smm_id', '!=', null)->where('targeting', 1)->where('editor_id', $user->id)->where('target_id', null)->orderBy('expires_at', 'ASC')->paginate(5);
                    $this->from = 0;
                    $this->count_posts = $this->posts->count();
                    $this->count = 1;
                }


                $text = "<b>Ожидает таргетирования в соц-сетях: </b> \n";
                $all_count_posts = Post::where('archived_at', null)->where('posting', 1)->where('smm_id', '!=', null)->where('targeting', 1)->where('editor_id', $user->id)->where('target_id', null)->count();
                break;
            case $this->statuses[8]:

                if($current_page && $current_page != 1) {
                    $this->posts = Post::where('archived_at', null)->where('posting', 1)->where('smm_id', '!=', null)->where('seeding', 1)->where('editor_id', $user->id)->where('seeder_id', null)->orderBy('expires_at', 'ASC')->paginate(($current_page + 1) * 5);
                    if(5 * $current_page < $this->posts->count())
                        $this->from = $this->posts->count() - 5;
                    else
                        $this->from = ($current_page - 1) * 5;

                    $this->count_posts = $this->posts->count();
                    $this->count = (($current_page * 5) - 5) + 1;

                } else {
                    $this->posts = Post::where('archived_at', null)->where('posting', 1)->where('smm_id', '!=', null)->where('seeding', 1)->where('editor_id', $user->id)->where('seeder_id', null)->orderBy('expires_at', 'ASC')->paginate(5);
                    $this->from = 0;
                    $this->count_posts = $this->posts->count();
                    $this->count = 1;
                }

                $text = "<b>Ожидает посева в соц-сетях: </b> \n";
                $all_count_posts = Post::where('archived_at', null)->where('posting', 1)->where('smm_id', '!=', null)->where('seeding', 1)->where('editor_id', $user->id)->where('seeder_id', null)->count();
                break;
            case $this->statuses[9]:

                if($current_page && $current_page != 1) {
                    $this->posts = Post::where('archived_at', null)->where('posting', 1)->where('smm_id', '!=', null)->where('commenting', 1)->where('editor_id', $user->id)->where('commentator_id', null)->orderBy('expires_at', 'ASC')->paginate(($current_page + 1) * 5);
                        if(5 * $current_page < $this->posts->count())
                            $this->from = $this->posts->count() - 5;
                        else
                            $this->from = ($current_page - 1) * 5;

                    $this->count_posts = $this->posts->count();
                    $this->count = (($current_page * 5) - 5) + 1;

                } else {
                    $this->posts = Post::where('archived_at', null)->where('posting', 1)->where('smm_id', '!=', null)->where('commenting', 1)->where('editor_id', $user->id)->where('commentator_id', null)->orderBy('expires_at', 'ASC')->paginate(5);
                    $this->from = 0;
                    $this->count_posts = $this->posts->count();
                    $this->count = 1;
                }

                $text = "<b>Ожидает комментирования в соц-сетях: </b> \n";
                $all_count_posts = Post::where('archived_at', null)->where('posting', 1)->where('smm_id', '!=', null)->where('commenting', 1)->where('editor_id', $user->id)->where('commentator_id', null)->count();
                break;
        }

        if($all_count_posts == 0) {
             $tasks_with_pagination['text'] = 'Нет задач по данному статусу';
             return $tasks_with_pagination;
        }


        $text .= $this->generateArrayPosts($this->posts, $this->from, $this->count_posts, $this->count);

        $status_task = ($status) ?  $status : 0;
        $pagination = $this->TasksPagination($current_page, $this->count_posts, $status_task);

        $tasks_with_pagination['keyboard'] = json_encode([
            'inline_keyboard' => [
                $pagination['keyboard'],
            ],
        ]);


        $tasks_with_pagination['text'] = $text;
        return $tasks_with_pagination;
    }

    /**
     * Задачи с пагинацией журналиста
     * @param $status
     * @param $user
     * @param null $current_page
     * @return array|string
     * @throws \TelegramBot\InlineKeyboardPagination\Exceptions\InlineKeyboardPaginationException
     */

    public function getTasksJournalist($status, $user, $current_page = null) {
        $tasks_with_pagination = [];
        $pagination = [];
        $text = '';
        $all_count_posts = 0;
        switch($status) {
            case $this->statuses[0]:
                $this->getPostsCounts($current_page, $user->id, 2, $this->role_journalist);
                $text = "<b>Без назначения: </b> \n";
                $all_count_posts = Post::where('archived_at', null)->where('status_id', 2)->where('journalist_id',null)->orderBy('expires_at', 'ASC')->count();
                break;
            case $this->statuses[1]:
                $this->getPostsCounts($current_page, $user->id, 1, $this->role_journalist);
                $text = "<b>Не в работе: </b> \n";
                $all_count_posts = Post::where('archived_at', null)->where('status_id', 1)->where('journalist_id',$user->id)->orderBy('expires_at', 'ASC')->count();
                break;
            case $this->statuses[2]:
                $this->getPostsCounts($current_page, $user->id, 3, $this->role_journalist);
                $text = "<b>В работе: </b> \n";
                $all_count_posts = Post::where('archived_at', null)->where('status_id', 3)->where('journalist_id',$user->id)->orderBy('expires_at', 'ASC')->count();
                break;
            case $this->statuses[3]:
                $this->getPostsCounts($current_page, $user->id, 4, $this->role_journalist);
                $text = "<b>Требуют проверки: </b> \n";
                $all_count_posts = Post::where('archived_at', null)->where('status_id', 4)->where('journalist_id',$user->id)->orderBy('expires_at', 'ASC')->count();
                break;
            case $this->statuses[4]:
                $this->getPostsCounts($current_page, $user->id, 5, $this->role_journalist);
                $text = "<b>На доработке: </b> \n";
                $all_count_posts = Post::where('archived_at', null)->where('status_id', 5)->where('journalist_id',$user->id)->orderBy('expires_at', 'ASC')->count();
                break;
            case $this->statuses[5]:
                $this->getPostsCounts($current_page, $user->id, 6, $this->role_journalist);
                $text = "<b>Ожидает публикации: </b> \n";
                $all_count_posts = Post::where('archived_at', null)->where('status_id', 6)->where('journalist_id',$user->id)->orderBy('expires_at', 'ASC')->count();
                break;
        }

        if($all_count_posts == 0) {
            return $tasks_with_pagination['text'] = 'Нет задач по данному статусу';
        }

        $text .= $this->generateArrayPosts($this->posts, $this->from, $this->count_posts, $this->count);

        $status_task = ($status) ?  $status : 0;
        $pagination = $this->TasksPagination($current_page, $this->count_posts, $status_task);

        $tasks_with_pagination['keyboard'] = json_encode([
            'inline_keyboard' => [
                $pagination['keyboard'],
            ],
        ]);

        $tasks_with_pagination['text'] = $text;

        return $tasks_with_pagination;
    }

    /**
     * Создание кнопок пагинации с callback датой по статусу
     * @param $current_page
     * @param $count_posts
     * @param $status_task
     * @return array
     * @throws \TelegramBot\InlineKeyboardPagination\Exceptions\InlineKeyboardPaginationException
     */

    private function TasksPagination($current_page, $count_posts, $status_task) {
        $items         = range(0,  $count_posts ); // required.
        $command       = 'takePosts'; // optional. Default: pagination
        $selected_page = ($current_page) ? $current_page : 1;            // optional. Default: 1
        $labels        = [              // optional. Change button labels (showing defaults)
            'default'  => '%d',
            'first'    => '« %d',
            'previous' => '‹ %d',
            'current'  => '· %d ·',
            'next'     => '%d ›',
            'last'     => '%d »',
        ];

        $status_task = str_replace('status_', '', $status_task);

// optional. Change the callback_data format, adding placeholders for data (showing default)
        $callback_data_format = 'command={COMMAND}&status_task='.$status_task.'&oldPage={OLD_PAGE}&newPage={NEW_PAGE}';



        // Define inline keyboard pagination.
        $ikp = new InlineKeyboardPagination($items, $command);
        $ikp->setMaxButtons(5, false); // Second parameter set to always show 7 buttons if possible.
        $ikp->setLabels($labels);
        $ikp->setCallbackDataFormat($callback_data_format);

// Get pagination.
        $pagination = $ikp->getPagination($selected_page);

// or, in 2 steps.
        $ikp->setSelectedPage($selected_page);


        return $pagination;
    }

    /**
     * Генерируем массив с задачами
     * @param $posts
     * @param $from
     * @param $count_posts
     * @param $count
     * @return string
     */
    private function generateArrayPosts($posts, $from, $count_posts, $count) {

        $text = '';

        if(!is_array($posts)) {
            $posts = $posts->all();
        }

        for($from; $from < $count_posts; $from++) {
            $deadline_post = $posts[$from]->expires_at->setTimezone('Europe/Moscow')->format('d-m-Y H:i') . ' (МСК)';
            $detail_post = new MessageEntity([
                'type' => 'bot_command',
                'offset' => 8,
                'length' => 10,
            ]);
            $detail_post['url'] = '/post'.$posts[$from]->id;
            $url = $detail_post->get('url');
            $text .=
                "Задача № ". $count .' (' .$posts[$from]->id . ") \n"
                . "<b>Название задачи: </b> " . $posts[$from]->title . " \n"
                . "<b>Дедлайн: </b> " . $deadline_post . "\n"
                . "<b>Подробнее: </b>" . $url . "\n"
                . " \n";

            $count++;
        }
        return $text;
    }

    /**
     * Получаем общее кол-во задач, получаем номер задач
     * Ничего не возвращается, свойствами класса оперируем
     * @param $current_page
     * @param $user_id
     * @param $status_id
     * @param $role
     */

    private function getPostsCounts($current_page, $user_id, $status_id, $role) {
        if($current_page && $current_page != 1) {
            if($role == $this->role_editor) {
                $this->posts = Post::where('archived_at', null)->where('status_id', $status_id)->where('editor_id', $user_id)->orderBy('expires_at', 'ASC')->paginate(($current_page + 1) * 5);
            } elseif($role == $this->role_journalist) {
                if($status_id == 2) {
                    $this->posts = Post::where('archived_at', null)->where('status_id', $status_id)->where('journalist_id', null)->orderBy('expires_at', 'ASC')->paginate(($current_page + 1) * 5);
                } else {
                    $this->posts = Post::where('archived_at', null)->where('status_id', $status_id)->where('journalist_id', $user_id)->orderBy('expires_at', 'ASC')->paginate(($current_page + 1) * 5);
                }
            }
            if(5 * $current_page < $this->posts->count())
                $this->from = $this->posts->count() - 5;
            else
                $this->from = ($current_page - 1) * 5;


            $this->count =  (($current_page * 5) - 5) + 1;
            $this->count_posts = $this->posts->count();
        } else {
            if($role == $this->role_editor) {
                $this->posts = Post::where('archived_at', null)->where('status_id', $status_id)->where('editor_id', $user_id)->orderBy('expires_at','ASC')->paginate(5);
            } elseif($role == $this->role_journalist) {
                if($status_id == 2) {
                    $this->posts = Post::where('archived_at', null)->where('status_id', $status_id)->where('journalist_id', null)->orderBy('expires_at','ASC')->paginate(5);
                } else {
                    $this->posts = Post::where('archived_at', null)->where('status_id', $status_id)->where('journalist_id', $user_id)->orderBy('expires_at','ASC')->paginate(5);

                }

            }

            $this->from = 0;
            $this->count_posts = $this->posts->count();
            $this->count = 1;
        }

    }


}
