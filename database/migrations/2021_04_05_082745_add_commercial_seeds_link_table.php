<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommercialSeedsLinkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_media_statistics_in_dashboard', function (Blueprint $table) {
            $table->unsignedBigInteger('post_seeds_links_id')->nullable();
            $table->foreign('post_seeds_links_id')->references('id')->on('commercial_seed_links')->onDelete('cascade');

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
