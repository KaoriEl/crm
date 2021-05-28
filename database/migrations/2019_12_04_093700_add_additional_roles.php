<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use App\Models\User;

class AddAdditionalRoles extends Migration
{
    private $roles = ['admin', 'targeter', 'seeder'];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->roles as $role) {
            Role::create(['name' => $role]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->roles as $role) {
            User::role($role)->get()->each(function ($user) use ($role) {
                $user->removeRole($role);
            });
        }

        Role::whereIn('name', $this->roles)->delete();
    }
}
