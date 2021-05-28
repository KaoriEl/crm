<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSocialMediaStatisticsInDashboardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_media_statistics_in_dashboard', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('post_smm_links_id')->nullable();
            $table->text('post_snippet')->nullable();
            $table->integer('views')->default(0);
            $table->integer('like')->default(0);
            $table->integer('count_comments')->default(0);
            $table->integer('reposts')->default(0);
            $table->integer('followers')->default(0);
            $table->foreign('post_smm_links_id')->references('id')->on('post_smm_links')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('social_media_statistics_in_dashboard');
    }
}
