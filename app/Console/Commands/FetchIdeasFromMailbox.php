<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FetchIdeasFromMailbox extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:mailbox:fetch-ideas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch the ideas from the mailbox';

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
        $client = \Webklex\IMAP\Facades\Client::account('default');
        $client->connect();

        $inbox = $client->getFolder('INBOX');

        $messages = $inbox->query()->unseen()->get();

        foreach ($messages as $email) {
            \App\Idea::createFromEmail($email);
        }
       */
    }
}
