<?php

use Illuminate\Database\Seeder;

class TimeScheduleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$temp = [
			"08:00 - 09:20", "09:30 - 10:50", "11:00 - 12:20", "12:30 - 13:50",
			"14:00 - 15:20", "15:30 - 16:50", "17:00 - 18:20", "18:30 - 19:50",
			
			"08:00 - 09:00", "09:10 - 10:10", "10:20 - 11:20", "11:30 - 12:30",
			"12:40 - 13:40", "13:50 - 14:50", "15:00 - 16:00", "16:10 - 17:10",

			"08:00 - 08:45", "09:00 - 09:45", "10:00 - 10:45", "11:00 - 11:45",
			"12:00 - 12:45", "13:00 - 13:45", "14:00 - 14:45", "15:00 - 15:45",
		];

		for($i = 0; $i < 3; $i++){
			for($j = 0; $j < 8; $j++){
				DB::table('time_schedule')->insert([
					'type' => $i+1,
					'lesson' => $j+1,
					'time' => $temp[$i*8 + $j],
					'institutionID' => 1
				]);
			}
		}
    }
}