<?php

use Illuminate\Database\Seeder;
use App\Models\StatusTask;

class StatusTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statusToCreate = ['не в работе', 'без назначения', 'в работе', 'нужна проверка', 'на доработке', 'ждет публикации', 'опубликовано'];

        foreach ($statusToCreate as $status) {
            StatusTask::create([
                'title' => $status,
            ]);
        }

    }
}
