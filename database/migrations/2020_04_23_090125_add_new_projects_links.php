<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewProjectsLinks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('y_dzen')->nullable()->default(null)->after("insta");
            $table->string('y_street')->nullable()->default(null)->after("insta");
            $table->string('yt')->nullable()->default(null)->after("insta");
            $table->string('tg')->nullable()->default(null)->after("insta");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('y_dzen');
            $table->dropColumn('y_street');
            $table->dropColumn('yt');
            $table->dropColumn('tg');
        });
    }
}
