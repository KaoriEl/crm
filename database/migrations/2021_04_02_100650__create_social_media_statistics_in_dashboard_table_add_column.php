<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSocialMediaStatisticsInDashboardTableAddColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_media_statistics_in_dashboard', function (Blueprint $table) {
            $table->string('acc_name')->nullable()->default("Имя аккаунта еще не полученно");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('social_media_statistics_in_dashboard', function (Blueprint $table) {
            //
        });
    }
}
