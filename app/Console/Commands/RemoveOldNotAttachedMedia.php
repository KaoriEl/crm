<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RemoveOldNotAttachedMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:remove-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove old and unattached media files';

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
        $now = new \DateTime;
        $now->modify('-1 day');

        $formattedDate = $now->format('Y-m-d H:i:s');

        $affectedRowsCount = \App\Media::leftJoin('mediables', 'mediables.media_id', '=', 'media.id')
            ->whereNull('mediables.media_id')
            ->where('created_at', '<=', $formattedDate)
            ->delete();

        $this->info('Удалено строк: ' . $affectedRowsCount);
        */
    }
}
