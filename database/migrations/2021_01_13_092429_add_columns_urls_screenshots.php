<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsUrlsScreenshots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->text('default_screenshot_url')->nullable();
            $table->text('vk_screenshot')->nullable();
            $table->text('ok_screenshot')->nullable();
            $table->text('fb_screenshot')->nullable();
            $table->text('ig_screenshot')->nullable();
            $table->text('y_dzen_screenshot')->nullable();
            $table->text('y_street_screenshot')->nullable();
            $table->text('yt_screenshot')->nullable();
            $table->text('tg_screenshot')->nullable();
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
            $table->dropColumn('default_screenshot_url');
            $table->dropColumn('vk_screenshot');
            $table->dropColumn('ok_screenshot');
            $table->dropColumn('fb_screenshot');
            $table->dropColumn('ig_screenshot');
            $table->dropColumn('y_dzen_screenshot');
            $table->dropColumn('y_street_screenshot');
            $table->dropColumn('yt_screenshot');
            $table->dropColumn('tg_screenshot');
        });
    }
}
