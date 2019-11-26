<?php

use Illuminate\Database\Seeder;

class DaysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$week = 1;

		// this year
		$year = 2019;
		for($month = 9; $month < 13; $month++){
			$days_in_month = date('t', mktime(0,0,0, $month,1,$year));
			
			for($day = 1; $day <= $days_in_month; $day++){
				$time = mktime(0, 0, 0, $month, $day, $year);
				$DofW = date('N', $time); //номер дня недели

				DB::table('days')->insert([
					'day' => $day,
					'month' => $month,
					'year' => $year,
					'DofW' => $DofW,
					'week' => $week,
				]);
				if($DofW == 7) $week++;
			}
		}

		//первый день это воскресенье
		DB::table('days')->delete(1);

		// other years
		for($year = 2020; $year < 2025; $year++){
			for($month = 1; $month < 13; $month++){
				$days_in_month = date('t', mktime(0,0,0, $month,1,$year));

				for($day = 1; $day <= $days_in_month; $day++){
					$time = mktime(0, 0, 0, $month, $day, $year);
					$DofW = date('N', $time); //номер дня недели

					DB::table('days')->insert([
						'day' => $day,
						'month' => $month,
						'year' => $year,
						'DofW' => $DofW,
						'week' => $week,
					]);
					if($DofW == 7) $week++;
				}
			}
		}
	}
}
