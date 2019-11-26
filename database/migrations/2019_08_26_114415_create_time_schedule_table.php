<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimeScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_schedule', function (Blueprint $table) {
            $table->smallIncrements('id');
			$table->smallInteger('type')->comment('1:80min, 2:60min, 3:45min');
			$table->smallInteger('lesson');
			$table->string('time', 15);
			$table->smallInteger('institutionID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('time_schedule');
    }
}
