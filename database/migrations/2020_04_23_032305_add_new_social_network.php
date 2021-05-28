<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewSocialNetwork extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('posting_to_y_dzen')->default(false)->after('posting_to_ig');
            $table->boolean('posting_to_y_street')->default(false)->after('posting_to_ig');
            $table->boolean('posting_to_yt')->default(false)->after('posting_to_ig');
            $table->boolean('posting_to_tg')->default(false)->after('posting_to_ig');

            $table->text('y_dzen_post_url')->nullable()->after('ig_post_url');
            $table->text('y_street_post_url')->nullable()->after('ig_post_url');
            $table->text('yt_post_url')->nullable()->after('ig_post_url');
            $table->text('tg_post_url')->nullable()->after('ig_post_url');

            $table->boolean('targeting_to_y_dzen')->default(false)->after('targeting_to_ig');
            $table->boolean('targeting_to_y_street')->default(false)->after('targeting_to_ig');
            $table->boolean('targeting_to_yt')->default(false)->after('targeting_to_ig');
            $table->boolean('targeting_to_tg')->default(false)->after('targeting_to_ig');

            $table->boolean('targeted_to_y_dzen')->default(false)->after('targeted_to_ig');
            $table->boolean('targeted_to_y_street')->default(false)->after('targeted_to_ig');
            $table->boolean('targeted_to_yt')->default(false)->after('targeted_to_ig');
            $table->boolean('targeted_to_tg')->default(false)->after('targeted_to_ig');

            $table->boolean('seeding_to_y_dzen')->default(false)->after('seeding_to_ok');
            $table->boolean('seeding_to_y_street')->default(false)->after('seeding_to_ok');
            $table->boolean('seeding_to_yt')->default(false)->after('seeding_to_ok');
            $table->boolean('seeding_to_tg')->default(false)->after('seeding_to_ok');



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

            $table->dropColumn('posting_to_y_dzen');
            $table->dropColumn('posting_to_y_street');
            $table->dropColumn('posting_to_yt');
            $table->dropColumn('posting_to_tg');

            $table->dropColumn('y_dzen_post_url');
            $table->dropColumn('y_street_post_url');
            $table->dropColumn('yt_post_url');
            $table->dropColumn('tg_post_url');

            $table->dropColumn('targeting_to_y_dzen');
            $table->dropColumn('targeting_to_y_street');
            $table->dropColumn('targeting_to_yt');
            $table->dropColumn('targeting_to_tg');

            $table->dropColumn('targeted_to_y_dzen');
            $table->dropColumn('targeted_to_y_street');
            $table->dropColumn('targeted_to_yt');
            $table->dropColumn('targeted_to_tg');

            $table->dropColumn('seeding_to_y_dzen');
            $table->dropColumn('seeding_to_y_street');
            $table->dropColumn('seeding_to_yt');
            $table->dropColumn('seeding_to_tg');


        });
    }
}
