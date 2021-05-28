<?php

namespace App\Console\Commands;
use App\Http\Controllers\Excel\Service\CronUpdate;
use App\Models\Jobs_cron;
use App\Statistics\CronUpdateStatistic;
use Illuminate\Console\Command;

class ComercialSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:updateCommercialSeeder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $job = Jobs_cron::where('job_name', 'update_comercial_seeder_statistic')->first();

        if(is_null($job)) {

            $job = new Jobs_cron([
                'job_name' => 'update_comercial_seeder_statistic',
            ]);
            $job->save();

            (new CronUpdateStatistic())->getComercialSeederLinks($job->job_name);

            $job->delete();
        }
    }

}
