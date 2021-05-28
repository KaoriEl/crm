<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->text('title')->nullable();
            $table->text('text')->nullable();

            $table->unsignedBigInteger('journalist_id')->nullable();
            $table->foreign('journalist_id')->references('id')->on('users');

            $table->text('draft_url')->nullable();
            $table->text('publication_url')->nullable();

            $table->boolean('posting')->default(false);
            $table->boolean('posting_to_vk')->default(false);
            $table->boolean('posting_to_ok')->default(false);
            $table->boolean('posting_to_fb')->default(false);
            $table->boolean('posting_to_ig')->default(false);
            $table->boolean('posting_to_y_dzen')->default(false);
            $table->boolean('posting_to_y_street')->default(false);
            $table->boolean('posting_to_yt')->default(false);
            $table->boolean('posting_to_tg')->default(false);

            $table->text('vk_post_url')->nullable();
            $table->text('ok_post_url')->nullable();
            $table->text('fb_post_url')->nullable();
            $table->text('ig_post_url')->nullable();
            $table->text('y_dzen_post_url')->nullable();
            $table->text('y_street_post_url')->nullable();
            $table->text('yt_post_url')->nullable();
            $table->text('tg_post_url')->nullable();

            $table->boolean('targeting')->default(false);
            $table->boolean('targeting_to_vk')->default(false);
            $table->boolean('targeting_to_ok')->default(false);
            $table->boolean('targeting_to_fb')->default(false);
            $table->boolean('targeting_to_ig')->default(false);
            $table->boolean('targeting_to_y_dzen')->default(false);
            $table->boolean('targeting_to_y_street')->default(false);
            $table->boolean('targeting_to_yt')->default(false);
            $table->boolean('targeting_to_tg')->default(false);

            $table->boolean('target_launched_in_vk')->default(false);
            $table->boolean('target_launched_in_ok')->default(false);
            $table->boolean('target_launched_in_fb')->default(false);
            $table->boolean('target_launched_in_ig')->default(false);
            $table->boolean('target_launched_in_tg')->default(false);
            $table->boolean('target_launched_in_yt')->default(false);
            $table->boolean('target_launched_in_y_street')->default(false);
            $table->boolean('target_launched_in_y_dzen')->default(false);

            $table->boolean('seeding')->default(false);
            $table->boolean('seeding_to_vk')->default(false);
            $table->boolean('seeding_to_ok')->default(false);
            $table->boolean('seeding_to_fb')->default(false);
            $table->boolean('seeding_to_insta')->default(false);
            $table->boolean('seeding_to_y_dzen')->default(false);
            $table->boolean('seeding_to_y_street')->default(false);
            $table->boolean('seeding_to_yt')->default(false);
            $table->boolean('seeding_to_tg')->default(false);
            $table->text('seed_list_url')->nullable();

            $table->boolean('commenting')->default(false);
            $table->boolean('commented')->default(false);
            $table->boolean('approved')->nullable()->default(null);

            $table->text('comment_after_moderating')->nullable();
            $table->timestamp('archived_at')->nullable();

            $table->unsignedBigInteger('project_id')->nullable();
            $table->foreign('project_id')->references('id')->on('projects');
            $table->boolean('status_task')->nullable()->default(null);
            $table->boolean('on_moderate')->nullable();
            $table->string('telegram_id')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temp_tasks');
    }
}
