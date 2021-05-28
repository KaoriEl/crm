<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckTelegramMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:telegram:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get messages from telegram';

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
     */

    public function handle()
    {
        /*
        $controller = new \App\Http\Controllers\TelegramBotController();
        $controller -> updatedActivity();
        */
    }
}
