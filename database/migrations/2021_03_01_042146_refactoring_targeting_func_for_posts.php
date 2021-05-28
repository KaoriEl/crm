<?php

use App\Models\Post;
use App\Models\SocialNetwork;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RefactoringTargetingFuncForPosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_networks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug');
            $table->string('icon');
            $table->string('link');
        });

        Schema::create('post_social_network', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('social_network_id');
            $table->unsignedBigInteger('price')->default(0);
            $table->boolean('sent_for_moderation')->default(false)->comment('Отправно ли на модерацию');
            $table->boolean('moderated')->nullable()->default(null)->comment('Прошла ли модерация');

            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('social_network_id')->references('id')->on('social_networks')->onDelete('cascade');
        });

        (new SocialNetworkSeeder)->run();
//        Переносим данные из таблицы posts в промежуточную таблицу
        $this->dataTransfer();

        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('targeting_to_vk');
            $table->dropColumn('targeting_to_ok');
            $table->dropColumn('targeting_to_fb');
            $table->dropColumn('targeting_to_ig');
            $table->dropColumn('targeting_to_tg');
            $table->dropColumn('targeting_to_yt');
            $table->dropColumn('targeting_to_y_street');
            $table->dropColumn('targeting_to_y_dzen');

            $table->dropColumn('targeted_to_vk');
            $table->dropColumn('targeted_to_ok');
            $table->dropColumn('targeted_to_fb');
            $table->dropColumn('targeted_to_ig');
            $table->dropColumn('targeted_to_tg');
            $table->dropColumn('targeted_to_yt');
            $table->dropColumn('targeted_to_y_street');
            $table->dropColumn('targeted_to_y_dzen');

            $table->dropColumn('target_launched_in_vk');
            $table->dropColumn('target_launched_in_ok');
            $table->dropColumn('target_launched_in_fb');
            $table->dropColumn('target_launched_in_ig');
            $table->dropColumn('target_launched_in_y_dzen');
            $table->dropColumn('target_launched_in_y_street');
            $table->dropColumn('target_launched_in_yt');
            $table->dropColumn('target_launched_in_tg');

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

    private function dataTransfer(): void
    {
        $posts = Post::get();

        foreach ($posts as $post) {
            if ($post->targeting) {
                $socialNetworks = [];
                if ($post->targeting_to_vk) {
                    $socialNetworks[SocialNetwork::where('name', 'ВК')->first()->id]['price'] = 0;
                    $socialNetworks[SocialNetwork::where('name', 'ВК')->first()->id]['sent_for_moderation'] = $post->targeted_to_vk;
                    $socialNetworks[SocialNetwork::where('name', 'ВК')->first()->id]['moderated'] = $post->target_launched_in_vk ? 1 : 0;
                }
                if ($post->targeting_to_ok) {
                    $socialNetworks[SocialNetwork::where('name', 'ОК')->first()->id]['price'] = 0;
                    $socialNetworks[SocialNetwork::where('name', 'ОК')->first()->id]['sent_for_moderation'] = $post->targeted_to_ok;
                    $socialNetworks[SocialNetwork::where('name', 'ОК')->first()->id]['moderated'] = $post->target_launched_in_ok ? 1 : 0;
                }
                if ($post->targeting_to_fb) {
                    $socialNetworks[SocialNetwork::where('name', 'FB')->first()->id]['price'] = 0;
                    $socialNetworks[SocialNetwork::where('name', 'FB')->first()->id]['sent_for_moderation'] = $post->targeted_to_fb;
                    $socialNetworks[SocialNetwork::where('name', 'FB')->first()->id]['moderated'] = $post->target_launched_in_fb ? 1 : 0;
                }
                if ($post->targeting_to_ig) {
                    $socialNetworks[SocialNetwork::where('name', 'Insta')->first()->id]['price'] = 0;
                    $socialNetworks[SocialNetwork::where('name', 'Insta')->first()->id]['sent_for_moderation'] = $post->targeted_to_ig;
                    $socialNetworks[SocialNetwork::where('name', 'Insta')->first()->id]['moderated'] = $post->target_launched_in_ig ? 1 : 0;
                }
                if ($post->targeting_to_y_dzen) {
                    $socialNetworks[SocialNetwork::where('name', 'Я.Д')->first()->id]['price'] = 0;
                    $socialNetworks[SocialNetwork::where('name', 'Я.Д')->first()->id]['sent_for_moderation'] = $post->targeted_to_y_dzen;
                    $socialNetworks[SocialNetwork::where('name', 'Я.Д')->first()->id]['moderated'] = $post->target_launched_in_y_dzen ? 1 : 0;
                }
                if ($post->targeting_to_y_street) {
                    $socialNetworks[SocialNetwork::where('name', 'Я.Р')->first()->id]['price'] = 0;
                    $socialNetworks[SocialNetwork::where('name', 'Я.Р')->first()->id]['sent_for_moderation'] = $post->targeted_to_y_street;
                    $socialNetworks[SocialNetwork::where('name', 'Я.Р')->first()->id]['moderated'] = $post->target_launched_in_y_street ? 1 : 0;
                }
                if ($post->targeting_to_yt) {
                    $socialNetworks[SocialNetwork::where('name', 'YT')->first()->id]['price'] = 0;
                    $socialNetworks[SocialNetwork::where('name', 'YT')->first()->id]['sent_for_moderation'] = $post->targeted_to_yt;
                    $socialNetworks[SocialNetwork::where('name', 'YT')->first()->id]['moderated'] = $post->target_launched_in_yt ? 1 : 0;
                }
                if ($post->targeting_to_tg) {
                    $socialNetworks[SocialNetwork::where('name', 'TG')->first()->id]['price'] = 0;
                    $socialNetworks[SocialNetwork::where('name', 'TG')->first()->id]['sent_for_moderation'] = $post->targeted_to_tg;
                    $socialNetworks[SocialNetwork::where('name', 'TG')->first()->id]['moderated'] = $post->target_launched_in_tg ? 1 : 0;
                }

                $post->socialNetworks()->sync($socialNetworks);
            }
        }
    }
}
