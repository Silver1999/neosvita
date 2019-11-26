<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$roles = [
			'Студент',
			'Заступник старости',
			'Староста',
			'Куратор',
			'Завідуючий відділенням',
			'Директор',
		];

		foreach($roles as $role){
			DB::table('roles')->insert([
				'roleName' => $role,
			]);
		}
    }
}
