<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnStatusInPostSocialNetworkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('post_social_network', function (Blueprint $table) {
            $table->enum('status', ['sent_for_moderation', 'successful_moderated', 'not_successful_moderated'])->nullable()->default(null);
        });

        $this->dataTransfer();
        Schema::table('post_social_network', function (Blueprint $table) {
            $table->dropColumn('sent_for_moderation');
            $table->dropColumn('moderated');
        });
    }

    private function dataTransfer()
    {
        $posts = Post::with('socialNetworks')->get();

        foreach ($posts as $post) {
            if ($post->targeting) {
                foreach ($post->socialNetworks() as $socialNetwork) {
                    $status = null;
                    if ((int)$socialNetwork->sent_for_moderation === 1) {
                        $status = \App\Enums\PostTargetStatusesEnum::SENT_FOR_MODERATION_STATUS;
                    }

                    if ((int)$socialNetwork->sent_for_moderation === 1 && (int)$socialNetwork->moderated === 0) {
                        $status = \App\Enums\PostTargetStatusesEnum::NOT_SUCCESSFUL_MODERATED_STATUS;
                    }

                    if ((int)$socialNetwork->sent_for_moderation === 1 && (int)$socialNetwork->moderated === 1) {
                        $status = \App\Enums\PostTargetStatusesEnum::SUCCESSFUL_MODERATED_STATUS;
                    }

                    $socialNetworkArray[$socialNetwork->id] = [
                        'status' => $status
                    ];

                    $post->socialNetworks()->updateExistingPivot($socialNetworkArray);
                }
            }
        }
    }
}
