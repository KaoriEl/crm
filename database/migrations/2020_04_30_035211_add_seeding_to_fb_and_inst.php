<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSeedingToFbAndInst extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('seeding_to_insta')->default(false)->after('seeding_to_ok');
            $table->boolean('seeding_to_fb')->default(false)->after('seeding_to_ok');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('seeding_to_fb');
            $table->dropColumn('seeding_to_insta');
        });
    }
}
