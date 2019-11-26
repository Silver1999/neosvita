<?php

use Illuminate\Database\Seeder;

class InstitutionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('institutions')->insert([
			'name' => 'КЕП ІФНТУНГ',
		]);
    }
}
