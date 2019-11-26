<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
		DB::table('users')->insert([
            'name' => 'Директор',
            'surname' => 'user_surname',
			'patronymic' => 'user_patronymic',
			'dayOfBirth' => date("Y-m-d"),
			'sex' => rand(1,2),
			'country' => 1,
			'city' => 1,
			'institution' => 1,
			'position' => 6,
			'group' => 0,
			'email' => 'a@a.a',
			'password' => Hash::make('123123'),
			'codeID' => 1,
		]);
		
		for ($group = 1; $group < 5; $group++) {
			DB::table('users')->insert([
				'name' => 'Завідуючий відділенням',
				'surname' => 'user_surname',
				'patronymic' => 'user_patronymic',
				'dayOfBirth' => date("Y-m-d"),
				'sex' => rand(1,2),
				'country' => 1,
				'city' => 1,
				'institution' => 1,
				'position' => 5,
				'group' => 0,
				'email' => $group.'superintendent@mail.com',
				'password' => Hash::make('123123'),
				'codeID' => 2,
			]);

			DB::table('users')->insert([
				'name' => 'Куратор',
				'surname' => 'user_surname',
				'patronymic' => 'user_patronymic',
				'dayOfBirth' => date("Y-m-d"),
				'sex' => rand(1,2),
				'country' => 1,
				'city' => 1,
				'institution' => 1,
				'position' => 4,
				'group' => $group,
				'email' => $group.'curator@mail.com',
				'password' => Hash::make('123123'),
				'codeID' => 3,
			]);

			DB::table('users')->insert([
				'name' => 'Староста',
				'surname' => 'user_surname',
				'patronymic' => 'user_patronymic',
				'dayOfBirth' => date("Y-m-d"),
				'sex' => rand(1,2),
				'country' => 1,
				'city' => 1,
				'institution' => 1,
				'position' => 3,
				'group' => $group,
				'email' => $group.'captain@mail.com',
				'password' => Hash::make('123123'),
				'codeID' => 4,
			]);

			DB::table('users')->insert([
				'name' => 'Заступник старости',
				'surname' => 'user_surname',
				'patronymic' => 'user_patronymic',
				'dayOfBirth' => date("Y-m-d"),
				'sex' => rand(1,2),
				'country' => 1,
				'city' => 1,
				'institution' => 1,
				'position' => 2,
				'group' => $group,
				'email' => $group.'deputy.headman@mail.com',
				'password' => Hash::make('123123'),
				'codeID' => 5,
			]);

			for ($i=1; $i < 4; $i++) { 
				DB::table('users')->insert([
					'name' => 'Студент_'. $i,
					'surname' => 'user_surname',
					'patronymic' => 'user_patronymic',
					'dayOfBirth' => date("Y-m-d"),
					'sex' => rand(1,2),
					'country' => 1,
					'city' => 1,
					'institution' => 1,
					'position' => 1,
					'group' => $group,
					'email' => $group.'student' . $i . '@mail.com',
					'password' => Hash::make('123123'),
					'codeID' => 5 + $i,
				]);
			}
		}
    }
}
