<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempIdeasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_ideas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->text('text');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('read_now')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temp_ideas');
    }
}
