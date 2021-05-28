<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsSmmIdAndTargetIdAndSeederIdAndCommentatorId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('smm_id')->nullable();
            $table->foreign('smm_id')->references('id')->on('users');
            $table->unsignedBigInteger('target_id')->nullable();
            $table->foreign('target_id')->references('id')->on('users');
            $table->unsignedBigInteger('seeder_id')->nullable();
            $table->foreign('seeder_id')->references('id')->on('users');
            $table->unsignedBigInteger('commentator_id')->nullable();
            $table->foreign('commentator_id')->references('id')->on('users');
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
            $table->dropForeign(['smm_id']);
            $table->dropColumn('smm_id');
            $table->dropForeign(['target_id']);
            $table->dropColumn('target_id');
            $table->dropForeign(['seeder_id']);
            $table->dropColumn('seeder_id');
            $table->dropForeign(['commentator_id']);
            $table->dropColumn('commentator_id');
        });
    }
}
