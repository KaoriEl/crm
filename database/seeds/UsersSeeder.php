<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
            'name'      => 'Администратор',
            'username'  => 'admin',
            'password'  => Hash::make('password'),
            'api_token' => \Str::random(80),
            'timezone'  => 'Etc/UTC',
        ]);

        $admin->assignRole(['editor', 'admin']);
    }
}
