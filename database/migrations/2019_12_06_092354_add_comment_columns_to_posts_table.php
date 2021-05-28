<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommentColumnsToPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('posting_text')->nullable();
            $table->string('targeting_text')->nullable();
            $table->string('seeding_text')->nullable();
            $table->string('commenting_text')->nullable();
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
            $table->dropColumn('posting_text');
            $table->dropColumn('targeting_text');
            $table->dropColumn('seeding_text');
            $table->dropColumn('commenting_text');
        });
    }
}