<?php

namespace App\Console\Commands;

use App\Http\Controllers\Excel\Service\CronUpdate;
use App\Models\Jobs_cron;
use App\Statistics\CronUpdateStatistic;
use Illuminate\Console\Command;

class CronUpdateDashboardStatistic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:updateSMMLinks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление статистики по SMM-ссылкам у постов';

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
        $job = Jobs_cron::where('job_name', 'update_social_statistic')->first();

        if(!is_null($job)) {
            $job = new Jobs_cron([
                'job_name' => 'update_social_statistic',
            ]);
            $job->save();

            (new CronUpdateStatistic())->getSMMLinks($job->job_name);

            $job->delete();
        }
    }
}
