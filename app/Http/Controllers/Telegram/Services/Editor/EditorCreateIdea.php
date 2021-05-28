<?php


namespace App\Http\Controllers\Telegram\Services\Editor;


use App\Http\Controllers\IdeaController;
use App\Http\Controllers\Telegram\Services\InterfaceCreateIdea;
use App\Http\Controllers\Telegram\Traits\TraitCreateIdea;
use App\Models\Idea;
use App\Models\Temp_idea;
use App\Models\User;
use Telegram\Bot\Keyboard\Keyboard;

class EditorCreateIdea implements InterfaceCreateIdea
{
   use TraitCreateIdea;
}
