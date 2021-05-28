<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rolesToCreate = ['editor', 'journalist', 'smm', 'commenter'];

        foreach ($rolesToCreate as $role) {
            Role::create([
                'name' => $role,
            ]);
        }
    }
}
