<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameTable extends Migration
{
    const FROM = 'caches';
    const TO = 'telegram_users';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename(self::FROM, self::TO);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename(self::TO, self::FROM);
    }
}
