<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skips', function (Blueprint $table) {
            $table->smallInteger('userID');
            $table->smallInteger('dayID');
            $table->smallInteger('week');
			$table->string('status', 80)->comment("0:очищено; 1:опоздал; 2:с причиной; 3:без причины");
			$table->smallInteger('pp');
			$table->smallInteger('bp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skips');
    }
}
