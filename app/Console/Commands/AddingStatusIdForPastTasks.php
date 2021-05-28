<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class AddingStatusIdForPastTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:updateStatusId';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status id column in tasks table';

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
        $status = 0;

        $tasks = Post::get()->whereNotNull('archived_at');
        foreach($tasks as $task) {
            $task->status_id = 7;
            $task->save();
        }



        $tasks = Post::get()->whereNull('archived_at');

        foreach($tasks as  $task) {
            if($task->status_task === null && $task->journalist_id === null)
                $status = 2;
            elseif($task->status_task === null && $task->journalist_id != null)
                $status = 1;
            elseif ($task->publication_url)
                $status = 7;
            elseif ($task->approved)
                $status = 6;
            elseif ($task->draft_url && $task->approved === null)
                $status = 4;
            elseif ($task->draft_url && !$task->approved)
                $status = 5;
            elseif ($task->hasJournalist())
                $status = 3;
            else
                $status = 1;

            $task->status_id = $status;
            $task->save();
        }

    }

}
