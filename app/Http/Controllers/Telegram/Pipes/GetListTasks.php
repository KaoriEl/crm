<?php


namespace App\Http\Controllers\Telegram\Pipes;
use App\Http\Controllers\Telegram\Traits\GetStatuses;
use App\Http\Controllers\Telegram\Traits\GetTasksByStatus;
use App\Models\Telegram_user;
use App\Models\User;

class GetListTasks
{
    use GetTasksByStatus;
    private $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function __invoke($returnData)
    {

        if($returnData['step'] == 'end') {
            return $returnData;
        }

        $telegram_user = Telegram_user::where('telegram_id', $returnData['user']['telegram_id'])->first();
        $user = $returnData['user']['obj_user'];
        // Если показать статусы задач
        if(isset($this->message['message']) && $this->message->getMessage()->text == 'Показать список задач'  && $telegram_user->role_id != null) {
            $returnData = $this->getAllTasks($returnData, $telegram_user);
        } elseif(isset($this->message['callback_query']) && $this->message->getCallbackQuery()->getData() == "take_tasks" && $telegram_user->role_id != null) {
            $returnData = $this->getAllTasks($returnData, $telegram_user);
        }elseif($telegram_user->role_id != null && $this->message->isType('callback_query') && stripos($this->message->getCallbackQuery()->getData(), 'status_') !== false) {
            // если нажали на задачи по определенному статусу
           $pagination_info =  $this->getPagesPagination($this->message);

           $tasks_with_pagination = [];

            if(empty($pagination_info)) {
                $tasks_with_pagination = $this->getTasks($this->message->getCallbackQuery()->getData(), $user, $telegram_user->role_id, null);
            } else {
                $tasks_with_pagination = $this->getTasks($pagination_info['status_task'], $user, $telegram_user->role_id, $pagination_info['new_page']);
            }


            if(isset($tasks_with_pagination['keyboard'])) {
                $returnData['keyboard']['isset'] = true;
                $returnData['keyboard']['obj_keyboard'] = $tasks_with_pagination['keyboard'];
                $returnData['text'] = $tasks_with_pagination['text'];
            } else {
                $returnData['text'] = $tasks_with_pagination['text'];
            }
            $returnData['step'] = 'end';
        }

        return $returnData;
    }

    /**
     * Получение страниц пагинации
     * @param $callbackData
     * @return array|false|string[]
     */
    private function getPagesPagination($callbackData)
    {
        $status_paginate = strpos($callbackData, 'takePosts');
        $info_in_paginate_button = [];
        $pagination_info = [];
        if ($status_paginate !== false) {
            $info_in_paginate_button = explode('&', $callbackData);
            foreach ($info_in_paginate_button as $key => $value) {
                $index = strpos($value, '=');
                $info_in_paginate_button[$key] = substr($value, $index + 1);
            }

        } else {
            return $pagination_info;
        }

        $pagination_info['old_page'] = $info_in_paginate_button[2];
        $pagination_info['new_page'] = stristr(array_pop($info_in_paginate_button), '"', true);
        $pagination_info['status_task'] = 'status_' . $info_in_paginate_button[1];
            return $pagination_info;
    }

    /**
     * Получаем статусы наших задач (избавление от дублирование кода)
     * @param $returnData
     * @param $telegram_user
     * @return mixed
     */
    private function getAllTasks($returnData, $telegram_user) {
        $returnData['keyboard']['isset'] = true;
        $user = User::where('telegram_id', $telegram_user->telegram_id)->first();
        $telegram_user->changeStepsUser('get_tasks', $telegram_user->current_step);
        $returnData['keyboard']['obj_keyboard'] = (new GetStatuses())->getStatuses($telegram_user->role_id, $user->id);

        if($returnData['keyboard']['obj_keyboard']->count() > 0) {
            $returnData['text'] = 'Выберите статус задач, которые необходимо отобразить.';
        } else {
            $returnData['keyboard']['isset'] = false;
            $returnData['text'] = 'Нет задач.';
        }

        $returnData['step'] = 'end';

        return $returnData;
    }

}
