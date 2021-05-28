<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->text('title');
            $table->text('text');

            $table->unsignedBigInteger('journalist_id')->nullable();
            $table->foreign('journalist_id')->references('id')->on('users');

            $table->text('draft_url')->nullable();
            $table->text('publication_url')->nullable();

            $table->boolean('posting')->default(false);
            $table->boolean('posting_to_vk')->default(false);
            $table->boolean('posting_to_ok')->default(false);
            $table->boolean('posting_to_fb')->default(false);
            $table->boolean('posting_to_ig')->default(false);

            $table->text('vk_post_url')->nullable();
            $table->text('ok_post_url')->nullable();
            $table->text('fb_post_url')->nullable();
            $table->text('ig_post_url')->nullable();

            $table->boolean('targeting')->default(false);
            $table->boolean('targeting_to_vk')->default(false);
            $table->boolean('targeting_to_ok')->default(false);
            $table->boolean('targeting_to_fb')->default(false);
            $table->boolean('targeting_to_ig')->default(false);

            $table->boolean('targeted_to_vk')->default(false);
            $table->boolean('targeted_to_ok')->default(false);
            $table->boolean('targeted_to_fb')->default(false);
            $table->boolean('targeted_to_ig')->default(false);

            $table->boolean('seeding')->default(false);
            $table->boolean('seeding_to_vk')->default(false);
            $table->boolean('seeding_to_ok')->default(false);
            $table->text('seed_list_url')->nullable();

            $table->boolean('commenting')->default(false);
            $table->boolean('commented')->default(false);
            $table->boolean('approved')->nullable()->default(null);

            $table->text('comment_after_moderating')->nullable();
            $table->timestamp('archived_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
