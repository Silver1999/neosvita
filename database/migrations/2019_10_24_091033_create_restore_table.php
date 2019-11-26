<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restores', function (Blueprint $table) {
			$table->string('email', 50)->unique();
			$table->bigInteger('userID')->unique();
			$table->string('restoreCode', 12);
			$table->unsignedBigInteger('time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restores');
    }
}
