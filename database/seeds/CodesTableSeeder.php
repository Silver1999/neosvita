<?php

use App\Functions;
use Illuminate\Database\Seeder;

class CodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
		DB::table('codes')->insert([
			'code' => 'iDZPkf0^l*!8iHS8&@vL',
			'roleID' => 6
		]);

		for($i = 1; $i < 7; $i++){
			DB::table('codes')->insert([
				'code' => Functions::codeGen(20),
				'roleID' => $i
			]);
		}
	}
}
