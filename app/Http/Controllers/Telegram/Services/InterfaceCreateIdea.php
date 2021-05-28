<?php


namespace App\Http\Controllers\Telegram\Services;


interface InterfaceCreateIdea
{
    public function createIdea($telegram_user, $message, $text_to_user) : array;
}
