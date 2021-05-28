<?php

use App\Models\User;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminProject = Project::create([
            'name'      => 'Основной проект',
            'description'  => '',
            'site'  => '',
            'vk'  => '',
            'ok'  => '',
            'fb'  => '',
            'insta'  => '',
        ]);
        $user = User::find(1);

        $user->projects()->sync($adminProject);

        $user->save();
    }
}
