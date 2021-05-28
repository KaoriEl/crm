<?php

use App\Models\Post;
use App\Models\SocialNetwork;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class TransferDataToPostSmmLinks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dataTransfer();

        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('vk_post_url');
            $table->dropColumn('ok_post_url');
            $table->dropColumn('fb_post_url');
            $table->dropColumn('ig_post_url');
            $table->dropColumn('tg_post_url');
            $table->dropColumn('yt_post_url');
            $table->dropColumn('y_street_post_url');
            $table->dropColumn('y_dzen_post_url');
            $table->dropColumn('tt_post_url');
        });
    }

    private function dataTransfer(): void
    {
        $posts = Post::get();
        foreach ($posts as $post) {
            if ($post->posting) {
                $socialNetworks = [];
                if ($post->vk_post_url) {
                    $socialNetworks[SocialNetwork::where('name', 'ВК')->first()->id]['link'] = $post->vk_post_url;
                }
                if ($post->ok_post_url) {
                    $socialNetworks[SocialNetwork::where('name', 'ОК')->first()->id]['link'] = $post->ok_post_url;
                }
                if ($post->fb_post_url) {
                    $socialNetworks[SocialNetwork::where('name', 'FB')->first()->id]['link'] = $post->fb_post_url ;
                }
                if ($post->ig_post_url) {
                    $socialNetworks[SocialNetwork::where('name', 'Insta')->first()->id]['link'] = $post->ig_post_url;
                }
                if ($post->tg_post_url) {
                    $socialNetworks[SocialNetwork::where('name', 'Я.Д')->first()->id]['link'] = $post->tg_post_url;
                }
                if ($post->yt_post_url) {
                    $socialNetworks[SocialNetwork::where('name', 'Я.Р')->first()->id]['link'] = $post->yt_post_url;
                }
                if ($post->y_street_post_url) {
                    $socialNetworks[SocialNetwork::where('name', 'YT')->first()->id]['link'] = $post->y_street_post_url;
                }
                if ($post->y_dzen_post_url) {
                    $socialNetworks[SocialNetwork::where('name', 'TG')->first()->id]['link'] = $post->y_dzen_post_url;
                }
                if ($post->tt_post_url) {
                    $socialNetworks[SocialNetwork::where('name', 'TT')->first()->id]['link'] = $post->tt_post_url;
                }
                $post->smmLinks()->sync($socialNetworks);
            }
        }
    }
}
