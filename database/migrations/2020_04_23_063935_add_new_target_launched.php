<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewTargetLaunched extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('target_launched_in_tg')->default(false)->after('target_launched_in_ig');
            $table->boolean('target_launched_in_yt')->default(false)->after('target_launched_in_ig');
            $table->boolean('target_launched_in_y_street')->default(false)->after('target_launched_in_ig');
            $table->boolean('target_launched_in_y_dzen')->default(false)->after('target_launched_in_ig');

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
            $table->dropColumn('target_launched_in_tg');
            $table->dropColumn('target_launched_in_yt');
            $table->dropColumn('target_launched_in_y_street');
            $table->dropColumn('target_launched_in_y_dzen');
        });
    }
}
