<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
			$table->bigIncrements('id');
			
            $table->string('name', 25);
            $table->string('surname', 25);
            $table->string('patronymic', 25);
			$table->date('dayOfBirth');
			$table->enum('sex', ['m', 'w']);
			
			$table->string('email', 50)->unique();
			$table->string('password');
			// $table->bigInteger('codeID')->unsigned()->unique();
			$table->bigInteger('codeID')->unsigned();

			$table->smallInteger('country');
			$table->smallInteger('city');
			$table->smallInteger('institution');
			$table->smallInteger('position');
			$table->smallInteger('group');

			$table->string('phone', 50)->nullable();
			$table->boolean('grant')->default(false);
			
			$table->rememberToken();
			$table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
