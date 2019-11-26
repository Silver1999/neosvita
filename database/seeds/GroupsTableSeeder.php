<?php

use Illuminate\Database\Seeder;

class GroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		for ($i=1; $i < 5; $i++) { 
			DB::table('groups')->insert([
				'name' => 'gr-0'.$i,
				'institutionID' => 1,
			]);
		}
	}
}
