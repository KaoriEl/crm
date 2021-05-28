<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Cache;

class RedirectAllUsersToCacheTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'registerTG:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Переносит всех юзеров с их telegram ID из таблицы users в таблицу Cache';

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
        $users = User::all()->where('telegram_id','!=', null);

        foreach($users as $user) {
            $cache = new Cache ([
                'username' => $user->phone,
                'telegram_id' => $user->telegram_id,
                'current_step' => 'start'
            ]);
            $cache->save();
        }
    }
}
