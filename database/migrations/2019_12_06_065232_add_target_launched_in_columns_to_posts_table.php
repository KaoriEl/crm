<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTargetLaunchedInColumnsToPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('target_launched_in_vk')->default(false);
            $table->boolean('target_launched_in_ok')->default(false);
            $table->boolean('target_launched_in_fb')->default(false);
            $table->boolean('target_launched_in_ig')->default(false);
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
            $table->dropColumn('target_launched_in_vk');
            $table->dropColumn('target_launched_in_ok');
            $table->dropColumn('target_launched_in_fb');
            $table->dropColumn('target_launched_in_ig');
        });
    }
}
