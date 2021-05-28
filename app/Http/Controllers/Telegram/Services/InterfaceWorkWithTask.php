<?php


namespace App\Http\Controllers\Telegram\Services;


interface InterfaceWorkWithTask
{
    public function workWithTask($user, $message) : array;
}
