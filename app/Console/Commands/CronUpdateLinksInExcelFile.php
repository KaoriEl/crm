<?php

namespace App\Console\Commands;

use App\Models\Jobs_cron;
use Illuminate\Console\Command;
use App\Http\Controllers\Excel\Service\CronUpdate;

class CronUpdateLinksInExcelFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'excel:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update statistic in Excel file';

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
        $job = Jobs_cron::where('job_name', 'update_statistic')->first();
        /**
         * В случае ошибок крона, если он падает например при отправке гуглом запросов, то он запускается с места на ссылки которой упал
         */
        if(!is_null($job)) {
            (new CronUpdate())->getUpdatesForCron($job);
        } else {
            $job = new Jobs_cron([
                'job_name' => 'update_statistic',
                'row_count' => 2,
                'list_name' => 'Лист 1'
            ]);
            $job->save();
            (new CronUpdate())->getUpdatesForCron($job);
        }
    }
}
