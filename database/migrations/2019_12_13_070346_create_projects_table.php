<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description')->nullable()->default(null);
            $table->string('site')->nullable()->default(null);
            $table->string('vk')->nullable()->default(null);
            $table->string('ok')->nullable()->default(null);
            $table->string('fb')->nullable()->default(null);
            $table->string('insta')->nullable()->default(null);
            $table->timestamp('archived_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
