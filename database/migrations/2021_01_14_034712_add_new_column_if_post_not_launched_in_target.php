<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnIfPostNotLaunchedInTarget extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('target_not_pass_moderation_in_vk')->default(false);
            $table->boolean('target_not_pass_moderation_in_ok')->default(false);
            $table->boolean('target_not_pass_moderation_in_fb')->default(false);
            $table->boolean('target_not_pass_moderation_in_ig')->default(false);
            $table->boolean('target_not_pass_moderation_in_yt')->default(false);
            $table->boolean('target_not_pass_moderation_in_tg')->default(false);
            $table->boolean('target_not_pass_moderation_in_y_dzen')->default(false);
            $table->boolean('target_not_pass_moderation_in_y_street')->default(false);
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
            $table->dropColumn('target_not_pass_moderation_in_vk');
            $table->dropColumn('target_not_pass_moderation_in_ok');
            $table->dropColumn('target_not_pass_moderation_in_fb');
            $table->dropColumn('target_not_pass_moderation_in_ig');
            $table->dropColumn('target_not_pass_moderation_in_yt');
            $table->dropColumn('target_not_pass_moderation_in_tg');
            $table->dropColumn('target_not_pass_moderation_in_y_dzen');
            $table->dropColumn('target_not_pass_moderation_in_y_street');
        });
    }
}
