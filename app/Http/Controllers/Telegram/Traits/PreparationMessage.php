<?php


namespace App\Http\Controllers\Telegram\Traits;


trait PreparationMessage
{

    /**
     * Обрабатываем сообщение для отправки юзеру
     * @param $returnData
     * @param $completeMessage
     * @return mixed
     */
    public function preparation($returnData, $completeMessage) {
        if(!isset($completeMessage['text'])) {
            $returnData['text'] = 'Напишите /start для работы с задачами.';
            $returnData['step'] = 'end';
            return $returnData;
        }
        $returnData['text'] = $completeMessage['text'];

        if(isset($completeMessage['keyboard']) && $completeMessage['keyboard']->count() > 0) {
            $returnData['keyboard']['isset'] = true;
            $returnData['keyboard']['obj_keyboard'] = $completeMessage['keyboard'];
        }

        $returnData['step'] = 'end';
        return $returnData;
    }

}
