<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommersSeederToPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->integer('commercial_seed')->after('posting')->default(0);
            $table->boolean('commercial_seed_to_vk')->default(false);
            $table->boolean('commercial_seed_to_ok')->default(false);
            $table->boolean('commercial_seed_to_fb')->default(false);
            $table->boolean('commercial_seed_to_ig')->default(false);
            $table->boolean('commercial_seed_to_y_dzen')->default(false);
            $table->boolean('commercial_seed_to_y_street')->default(false);
            $table->boolean('commercial_seed_to_yt')->default(false);
            $table->boolean('commercial_seed_to_tg')->default(false);
            $table->boolean('commercial_seed_to_tt')->default(false);
            $table->string('commercial_seed_text')->nullable();

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
            //
        });
    }
}
