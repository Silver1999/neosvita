<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersTableStructureMod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
		Schema::table('users', function (Blueprint $table) {
			$table->string('addressOfResidence', 50)->nullable();
			$table->string('addressOfRegistration', 50)->nullable();
			$table->string('parentName1', 75)->nullable();
			$table->string('parentName2', 75)->nullable();
			$table->string('parentPhone', 50)->nullable();
			// ALTER TABLE `users` ADD `test` VARCHAR(50) NULL DEFAULT NULL;
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
		Schema::table('users', function (Blueprint $table) {
			$table->dropColumn('addressOfResidence');
			$table->dropColumn('addressOfRegistration');
			$table->dropColumn('parentName1');
			$table->dropColumn('parentName2');
			$table->dropColumn('parentPhone');
		});
    }
}
