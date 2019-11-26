<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuperintendentGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
		DB::table('superintendent_groups')
		->insert([
			['userID' => 2, 'groupID' => 1],
			['userID' => 2, 'groupID' => 2],
			['userID' => 9, 'groupID' => 3],
		]);
    }
}
