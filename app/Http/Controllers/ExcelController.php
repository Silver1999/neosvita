<?php

namespace App\Http\Controllers;

use App\Mail\SendExcelMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelController extends Controller
{
	public function getExcel($type) {
		$tableType = session()->get('export')['type'];
		if(!$tableType) return '';

		if (empty(session()->get('user')->position)) {
			$permission = 1;
		} else {
			$permission = session()->get('user')->position;
		};

		// экспорт или расслыка
		$type = $type == 'send' ? 'send' : 'export';

		if($type == 'send' && $permission < 4) return '';

		switch($tableType){
			case 'week':
				$this->getWeekExcel(session()->get('export')['data'], $type);
				break;
			case 'month':
				$this->getMonthExcel(session()->get('export')['data'], $type);
				break;
			case 'year':
				$this->getYearExcel(session()->get('export')['data'], $type);
				break;
		}
	}
	
	public function getWeekExcel($arr, $type){
		$groupID = $arr['groupID'];

		if(!empty($groupID)){
			$users = $this->getUsers($groupID);
		}

		if(!empty($users)) {
			$userIDs = [];
			foreach($users as $user){
				$userIDs[] = $user->id;
			}

			$discipleNames = DB::table('schedule')
				->select('dayID', 'content')
				->where([
					'groupID' => $groupID
				]);

			// дни выбранной недели
			$days = DB::table('days')
				->select('days.*', 'content')
				->where([
					'week' => $arr['currentWeek'],
					['DofW', '<', 7]
				])
				->orderBy('days.id', 'asc')
				->leftJoinSub($discipleNames, 'day_id', function($join) {
					$join->on('dayID', '=', 'id');
					})
				->get();

			$dayIDs = [];
			foreach($days as $day){
				$dayIDs[] = $day->id;
			}
		}

		if(!empty($userIDs) && !empty($dayIDs)){
			$skips = DB::table('skips')
				->whereIn('dayID', $dayIDs)
				->whereIn('userID', $userIDs)
				->get();
		}

		$skips_sorted = [];
		foreach($skips as $skip){
			$skips_sorted[$skip->userID][$skip->dayID] = $skip->status;
		}

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
		$sheet->setCellValue('A1', '№');
		$sheet->setCellValue('B1', 'ПРІЗВИЩЕ ТА' . PHP_EOL . 'ІНІЦІАЛИ СТУДЕНТА');
		$sheet->mergeCells('A1:A2');
		$sheet->mergeCells('B1:B2');
		
		$sheet->getDefaultColumnDimension()->setWidth(5);
		$sheet->getColumnDimension('A')->setWidth(5);
		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getRowDimension('1')->setRowHeight(30);
		$sheet->getRowDimension('2')->setRowHeight(200);

		$sheetVars = [];
		$dayNames = ["ПОНЕДІЛОК", "ВІВТОРОК", "СЕРЕДА", "ЧЕТВЕР", "П’ЯТНИЦЯ", "СУБОТА"];
		$i = 0;
		$firstCell = "C";
		$lastCell = "C";
		$daysLastCells = [];
		foreach($days as $day){
			$unserialized = unserialize($day->content);
			$sheet->setCellValue($firstCell.'1', sprintf('%s %02d.%02d.%d', $dayNames[$i], $day->day, $day->month, $day->year));

			for($j = 0; $j < 8; $j++){
				$sheet->setCellValue($lastCell.'2', '    ' . ($j+1) . '.  ' . $unserialized[$j][0]);
				if($j < 7){
					$lastCell++;
				} else {
					$daysLastCells[] = $lastCell;
				}
			}

			$sheetVars['lastDiscipline'] = $lastCell;
			$sheet->mergeCells($firstCell . '1:' . $lastCell . '1');
			$firstCell = ++$lastCell;
			$i++;
		}

		//Ячейки сумм
		$sheetVars['summCell'] = $lastCell; //первая ячейка сумм

		$sheet->mergeCells($lastCell . '1:' . $lastCell . '2');
		$sheet->setCellValue($lastCell . '1', "ПРОПУСКИ ПО" . PHP_EOL . "ПОВАЖНІЙ ПРИЧИНІ");
		$sheet->getColumnDimension($lastCell)->setWidth(10);
		$lastCell++;

		$sheet->mergeCells($lastCell . '1:' . $lastCell . '2');
		$sheet->setCellValue($lastCell . '1', "ПРОПУСКИ БЕЗ" . PHP_EOL . "ПОВАЖНІЙ ПРИЧИНІ");
		$sheet->getColumnDimension($lastCell)->setWidth(10);
		$lastCell++;

		$sheet->mergeCells($lastCell . '1:' . $lastCell . '2');
		$sheet->setCellValue($lastCell . '1', "ЗАГАЛЬНА КІЛЬКІСТЬ" . PHP_EOL . "ПРОПУСКІВ");
		$sheet->getColumnDimension($lastCell)->setWidth(10);
		//---------------------------------------------------------------

		$sheetVars['lastCell'] = $lastCell;

		//------------------ users + skips ----------------
		$i = 1;
		foreach($users as $user){
			$sheet->getRowDimension($i+2)->setRowHeight(27);
			$sheet->setCellValue('A' . ($i+2), $i);
			$sheet->setCellValue('B'. ($i+2) , $user->surname.'. '.mb_substr($user->name, 0 , 1).'.'.mb_substr($user->patronymic, 0 , 1).'.');
			$lastCell = 'C';
			$pp = 0;
			$bp = 0;

			foreach($days as $day){
				if(empty($skips_sorted[$user->id][$day->id])){
					for($j = 0; $j < 8; $j++) $lastCell++;
				} else {
					$skips = unserialize($skips_sorted[$user->id][$day->id]);

					foreach ($skips as $skip) {
						$currentCell = $lastCell . ($i+2);
						$sheet->getStyle($currentCell)->getFill()->setFillType('solid');
						switch($skip){
							case 1: 
								$sheet->setCellValue($currentCell, 'СП');
								$sheet->getStyle($currentCell)->getFill()->getStartColor()->setARGB('00d3d073');
								break;
							case 2: 
								$sheet->setCellValue($currentCell, 'ПП');
								$sheet->getStyle($currentCell)->getFill()->getStartColor()->setARGB('00acd373');
								$pp++;
								break;
							case 3: 
								$sheet->setCellValue($currentCell, 'БП');
								$sheet->getStyle($currentCell)->getFill()->getStartColor()->setARGB('00d37373');
								$bp++;
								break;
						}
						$lastCell++;
					}
				}
			}
			
			$temp = $lastCell;
			$sheet->setCellValue($temp++ . ($i+2), $pp);
			$sheet->setCellValue($temp++ . ($i+2), $bp);
			$sheet->setCellValue($temp . ($i+2), $bp + $pp);
			$i++;
		}

		//----------------- Styles -------------------
		$i++;

		$sheet->getStyle('A1:' . $sheetVars['lastCell'] . $i)->applyFromArray($this->getStyleGlobal());
		$sheet->getStyle('A1:B2')->applyFromArray($this->getStyleHeader());
		$sheet->getStyle('C1:'. $sheetVars['lastCell']. '1')->applyFromArray($this->getStyleHeader());
		$sheet->getStyle($sheetVars['summCell'] . '1:' . $sheetVars['lastCell']. '2')->applyFromArray($this->getStyleHeaderSumm());
		$sheet->getStyle('C2:'. $sheetVars['lastDiscipline']. '2')->applyFromArray($this->getStyleDiscipline());
		$sheet->getStyle('A3:B'. $i)->applyFromArray($this->getStyleUserNames());
		$sheet->getStyle($sheetVars['summCell'] . '3:'.$sheetVars['lastCell']. $i)->applyFromArray($this->getStyleBold());

		$temp = $this->getStyleDayDelimiter();
		array_pop($daysLastCells);
		foreach($daysLastCells as $daysLastCell){
			$sheet->getStyle($daysLastCell.'2:'.$daysLastCell.$i)->applyFromArray($temp);
		}
		//---------------------------------------------
		$writer = new Xlsx($spreadsheet);

		if($type == 'send'){
			$emails = [];
			foreach($users as $user){
				$emails[] = $user->email;
			}
			if(count($emails)){
				$fileName = public_path() . '\\' . session()->get('_token') . '.xlsx';
				$writer->save($fileName);
				$this->send($fileName, $emails);
				if(file_exists($fileName)) unlink($fileName);
			} else return 'no_emails';
			return 'ok';
		} else {
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="file.xlsx"');
			$writer->save("php://output");
		}

		
	}

	public function getMonthExcel($arr, $type){
		$groupID = $arr['groupID'];

		if(!empty($groupID)){
			$users = $this->getUsers($groupID);
		}

		$userIDs = [];
		if(!empty($users)) {
			foreach($users as $user){
				$userIDs[] = $user->id;
			}

			$days = DB::table('days')
			->where([
				'year' => $arr['year'],
				'month' => $arr['month'],
				['DofW', '<', 7],
			])
			->get();

			$dayShift[0] = $days->first()->DofW - 1;
			$dayShift[1] = 6 - $days->last()->DofW;
			$days_sorted = [];

			for($i = 0; $i < $dayShift[0]; $i++){
				$days_sorted[$days->first()->week][] = null;
			}

			foreach($days as $day){
				$days_sorted[$day->week][] = $day;
			}

			for($i = 0; $i < $dayShift[1]; $i++){
				$days_sorted[$days->last()->week][] = null;
			}

			$dayIDs = [];
			foreach($days as $day){
				$dayIDs[] = $day->id;
			}
		}

		if(!empty($userIDs)){
			$skips = DB::table('skips')
				->select('dayID', 'userID', 'pp', 'bp')
				->whereIn('userID', $userIDs)
				->whereBetween('dayID', [$days->first()->id, $days->last()->id])
				->get();
		}

		$skips_sorted = [];
		foreach($skips as $skip){
			$skips_sorted[$skip->userID][$skip->dayID] = $skip;
		}
		
		unset($days, $skips);

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
		$sheet->setCellValue('A1', '№');
		$sheet->setCellValue('B1', 'ПРІЗВИЩЕ ТА' . PHP_EOL . 'ІНІЦІАЛИ СТУДЕНТА');
		$sheet->mergeCells('A1:A2');
		$sheet->mergeCells('B1:B2');
		
		$sheet->getDefaultColumnDimension()->setWidth(5);
		$sheet->getColumnDimension('A')->setWidth(5);
		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getRowDimension('1')->setRowHeight(30);
		$sheet->getRowDimension('2')->setRowHeight(200);

		$sheetVars = [];
		$dayNames = ["ПОНЕДІЛОК", "ВІВТОРОК", "СЕРЕДА", "ЧЕТВЕР", "П’ЯТНИЦЯ", "СУБОТА"];
		$weekNames = ["I", "II", "III", "IV", "V", "VI"];
		$i = 0;
		$firstCell = "C";
		$lastCell = "C";
		$daysLastCells = [];
		foreach($days_sorted as $week){
			$sheet->setCellValue($firstCell.'1', $weekNames[$i].' ТИЖДЕНЬ');

			for($j = 0; $j < 6; $j++){
				$day = $week[$j];
				if(empty($day)){
					$sheet->setCellValue($lastCell.'2', '');
				} else {
					$sheet->setCellValue($lastCell.'2', sprintf('%s %02d.%02d.%d', $dayNames[$j], $day->day, $day->month, $day->year));
				}

				if($j < 5){
					$lastCell++;
				} else {
					$daysLastCells[] = $lastCell;
				}
			}

			$sheetVars['lastDiscipline'] = $lastCell;
			$sheet->mergeCells($firstCell . '1:' . $lastCell . '1');
			$firstCell = ++$lastCell;
			$i++;
		}

		//Ячейки сумм
		$sheetVars['summCell'] = $lastCell; //первая ячейка сумм

		$sheet->mergeCells($lastCell . '1:' . $lastCell . '2');
		$sheet->setCellValue($lastCell . '1', "ПРОПУСКИ ПО" . PHP_EOL . "ПОВАЖНІЙ ПРИЧИНІ");
		$sheet->getColumnDimension($lastCell)->setWidth(10);
		$lastCell++;

		$sheet->mergeCells($lastCell . '1:' . $lastCell . '2');
		$sheet->setCellValue($lastCell . '1', "ПРОПУСКИ БЕЗ" . PHP_EOL . "ПОВАЖНІЙ ПРИЧИНІ");
		$sheet->getColumnDimension($lastCell)->setWidth(10);
		$lastCell++;

		$sheet->mergeCells($lastCell . '1:' . $lastCell . '2');
		$sheet->setCellValue($lastCell . '1', "ЗАГАЛЬНА КІЛЬКІСТЬ" . PHP_EOL . "ПРОПУСКІВ");
		$sheet->getColumnDimension($lastCell)->setWidth(10);
		//---------------------------------------------------------------

		$sheetVars['lastCell'] = $lastCell;

		//------------------ users + skips ----------------
		$i = 1;
		foreach($users as $user){
			$sheet->getRowDimension($i+2)->setRowHeight(27);
			$sheet->setCellValue('A' . ($i+2), $i);
			$sheet->setCellValue('B'. ($i+2) , $user->surname.'. '.mb_substr($user->name, 0 , 1).'.'.mb_substr($user->patronymic, 0 , 1).'.');
			$lastCell = 'C';
			$pp = 0;
			$bp = 0;

			foreach($days_sorted as $week){
				foreach($week as $day){
					if(empty($day)){
						$userSkip = '';
					} else {
						$userSkip = $skips_sorted[$user->id][$day->id] ?? '';
					}

					if(empty($userSkip)) {
						$lastCell++;
					} else {
						$currentCell = $lastCell . ($i+2);
						if($userSkip->pp + $userSkip->bp){
							$sheet->setCellValue($currentCell, $userSkip->pp + $userSkip->bp);
						}
						$pp += $userSkip->pp;
						$bp += $userSkip->bp;
						$lastCell++;
					}
				}
			}
			
			$temp = $lastCell;
			$sheet->setCellValue($temp++ . ($i+2), $pp);
			$sheet->setCellValue($temp++ . ($i+2), $bp);
			$sheet->setCellValue($temp . ($i+2), $pp + $bp);
			$i++;
		}

		//----------------- Styles -------------------
		$i++;

		$sheet->getStyle('A1:' . $sheetVars['lastCell'] . $i)->applyFromArray($this->getStyleGlobal());
		$sheet->getStyle('A1:B2')->applyFromArray($this->getStyleHeader());
		$sheet->getStyle('C1:'. $sheetVars['lastCell']. '1')->applyFromArray($this->getStyleHeader());
		$sheet->getStyle($sheetVars['summCell'] . '1:' . $sheetVars['lastCell']. '2')->applyFromArray($this->getStyleHeaderSumm());
		$sheet->getStyle('C2:'. $sheetVars['lastDiscipline']. '2')->applyFromArray($this->getStyleDisciplineCenter());
		$sheet->getStyle('A3:B'. $i)->applyFromArray($this->getStyleUserNames());
		$sheet->getStyle($sheetVars['summCell'] . '3:'.$sheetVars['lastCell']. $i)->applyFromArray($this->getStyleBold());

		$temp = $this->getStyleDayDelimiter();
		array_pop($daysLastCells);
		foreach($daysLastCells as $daysLastCell){
			$sheet->getStyle($daysLastCell.'2:'.$daysLastCell.$i)->applyFromArray($temp);
		}
		//---------------------------------------------

		$writer = new Xlsx($spreadsheet);

		if($type == 'send'){
			$emails = [];
			foreach($users as $user){
				$emails[] = $user->email;
			}
			if(count($emails)){
				$fileName = public_path() . '\\' . session()->get('_token') . '.xlsx';
				$writer->save($fileName);
				$this->send($fileName, $emails);
				if(file_exists($fileName)) unlink($fileName);
			} else return 'no_emails';
			return 'ok';
		} else {
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="file.xlsx"');
			$writer->save("php://output");
		}

	}

	public function getYearExcel($arr, $type){
		$groupID = $arr['groupID'];

		if(!empty($groupID)){
			$users = $this->getUsers($groupID);
		}

		$userIDs = [];
		if(!empty($users)) {
			foreach($users as $user){
				$userIDs[] = $user->id;
			}

			$days = DB::table('days')
			->select('month', 'week')
			->where([
				'year' => $arr['year'],
				['DofW', '<', '7'],
			])
			->whereBetween('month', $arr['month'])
			->distinct()
			->get();

			$days_sorted = [];
			foreach($days as $day){
				$days_sorted[$day->month][] = $day->week;
			}
		}

		if(!empty($userIDs)){
			$skips = DB::table('skips')
				->select('userID', 'week', DB::raw('SUM(pp) as pp'), DB::raw('SUM(bp) as bp'))
				->whereIn('userID', $userIDs)
				->whereBetween('week', [$days->first()->week, $days->last()->week])
				->groupBy('userID', 'week')
				->distinct()
				->get();
		}

		$skips_sorted = [];
		foreach($skips as $skip){
			$skips_sorted[$skip->userID][$skip->week] = $skip;
		}
		
		unset($days, $skips);

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
		$sheet->setCellValue('A1', '№');
		$sheet->setCellValue('B1', 'ПРІЗВИЩЕ ТА' . PHP_EOL . 'ІНІЦІАЛИ СТУДЕНТА');
		$sheet->mergeCells('A1:A2');
		$sheet->mergeCells('B1:B2');
		
		$sheet->getDefaultColumnDimension()->setWidth(5);
		$sheet->getColumnDimension('A')->setWidth(5);
		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getRowDimension('1')->setRowHeight(30);
		$sheet->getRowDimension('2')->setRowHeight(200);

		$sheetVars = [];
		$weekNames = ["I", "II", "III", "IV", "V", "VI"];
		$monthNames = [
			'1' => 'Січень', '2' => 'Лютий', '3' => 'Березень', '4' => 'Квітень', '5' => 'Травень', 
			'6' => 'Червень', '9' => 'Вересень', '10' => 'Жовтень', '11' => 'Листопад', '12' => 'Грудень',
		];

		$i = 0;
		$firstCell = "C";
		$lastCell = "C";
		$daysLastCells = [];
		foreach($days_sorted as $key => $month){
			$sheet->setCellValue($firstCell.'1', $monthNames[$key]);

			for($j = 0; $j < count($month); $j++){
				$sheet->setCellValue($lastCell.'2', $weekNames[$j] . ' Тиждень');

				if($j < count($month) - 1){
					$lastCell++;
				} else {
					$daysLastCells[] = $lastCell;
				}
			}

			$sheetVars['lastDiscipline'] = $lastCell;
			$sheet->mergeCells($firstCell . '1:' . $lastCell . '1');
			$firstCell = ++$lastCell;
			$i++;
		}

		//Ячейки сумм
		$sheetVars['summCell'] = $lastCell; //первая ячейка сумм

		$sheet->mergeCells($lastCell . '1:' . $lastCell . '2');
		$sheet->setCellValue($lastCell . '1', "ПРОПУСКИ ПО" . PHP_EOL . "ПОВАЖНІЙ ПРИЧИНІ");
		$sheet->getColumnDimension($lastCell)->setWidth(10);
		$lastCell++;

		$sheet->mergeCells($lastCell . '1:' . $lastCell . '2');
		$sheet->setCellValue($lastCell . '1', "ПРОПУСКИ БЕЗ" . PHP_EOL . "ПОВАЖНІЙ ПРИЧИНІ");
		$sheet->getColumnDimension($lastCell)->setWidth(10);
		$lastCell++;

		$sheet->mergeCells($lastCell . '1:' . $lastCell . '2');
		$sheet->setCellValue($lastCell . '1', "ЗАГАЛЬНА КІЛЬКІСТЬ" . PHP_EOL . "ПРОПУСКІВ");
		$sheet->getColumnDimension($lastCell)->setWidth(10);
		//---------------------------------------------------------------

		$sheetVars['lastCell'] = $lastCell;

		//------------------ users + skips ----------------
		$i = 1;
		foreach($users as $user){
			$sheet->getRowDimension($i+2)->setRowHeight(27);
			$sheet->setCellValue('A' . ($i+2), $i);
			$sheet->setCellValue('B'. ($i+2) , $user->surname.'. '.mb_substr($user->name, 0 , 1).'.'.mb_substr($user->patronymic, 0 , 1).'.');
			$lastCell = 'C';
			$pp = 0;
			$bp = 0;

			foreach($days_sorted as $month){
				foreach($month as $week){
					if(empty($week)){
						$userSkip = '';
					} else {
						$userSkip = $skips_sorted[$user->id][$week] ?? '';
					}

					if(empty($userSkip)) {
						$lastCell++;
					} else {
						$currentCell = $lastCell . ($i+2);
						if($userSkip->pp + $userSkip->bp){
							$sheet->setCellValue($currentCell, $userSkip->pp + $userSkip->bp);
						}
						$pp += $userSkip->pp;
						$bp += $userSkip->bp;
						$lastCell++;
					}
				}
			}
			
			$temp = $lastCell;
			$sheet->setCellValue($temp++ . ($i+2), $pp);
			$sheet->setCellValue($temp++ . ($i+2), $bp);
			$sheet->setCellValue($temp . ($i+2), $pp + $bp);
			$i++;
		}

		//----------------- Styles -------------------
		$i++;

		$sheet->getStyle('A1:' . $sheetVars['lastCell'] . $i)->applyFromArray($this->getStyleGlobal());
		$sheet->getStyle('A1:B2')->applyFromArray($this->getStyleHeader());
		$sheet->getStyle('C1:'. $sheetVars['lastCell']. '1')->applyFromArray($this->getStyleHeader());
		$sheet->getStyle($sheetVars['summCell'] . '1:' . $sheetVars['lastCell']. '2')->applyFromArray($this->getStyleHeaderSumm());
		$sheet->getStyle('C2:'. $sheetVars['lastDiscipline']. '2')->applyFromArray($this->getStyleDisciplineCenter());
		$sheet->getStyle('A3:B'. $i)->applyFromArray($this->getStyleUserNames());
		$sheet->getStyle($sheetVars['summCell'] . '3:'.$sheetVars['lastCell']. $i)->applyFromArray($this->getStyleBold());

		$temp = $this->getStyleDayDelimiter();
		array_pop($daysLastCells);
		foreach($daysLastCells as $daysLastCell){
			$sheet->getStyle($daysLastCell.'2:'.$daysLastCell.$i)->applyFromArray($temp);
		}
		//---------------------------------------------

		$writer = new Xlsx($spreadsheet);

		if($type == 'send'){
			$emails = [];
			foreach($users as $user){
				$emails[] = $user->email;
			}
			if(count($emails)){
				$fileName = public_path() . '\\' . session()->get('_token') . '.xlsx';
				$writer->save($fileName);
				$this->send($fileName, $emails);
				if(file_exists($fileName)) unlink($fileName);
			} else return 'no_emails';
			return 'ok';
		} else {
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="file.xlsx"');
			$writer->save("php://output");
		}

	}
//----------------------------------------------
	public function getUsers($groupID){
		if(!empty($groupID)){
			return DB::table('users')
			->select('id', 'name', 'surname', 'patronymic', 'position', 'email')
			->where([
				'group' => $groupID,
				['position', '<', 4]
			])
			->orderBy('surname', 'asc')
			->get();
		} else {
			return 0;
		}
	}
//----------------------------------------------
	public function getStyleGlobal(){
		return [
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			],
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => ['argb' => 'FF8fa7c9'],
				],
			],
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
			],
		];
	}

	public function getStyleHeader(){
		return [
			'font' => [
				'bold' => true,
			],
			'alignment' => [
				// 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				// 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				'wrapText' => true,
			],
			'fill' => [
				// 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'color' => ['argb' => '00bfcee5'],
			],
		];
	}

	public function getStyleHeaderSumm(){
		return [
			'alignment' => [
				'textRotation' => 90,
				'wrapText' => true,
			],
		];
	}

	public function getStyleDiscipline(){
		return [
			'alignment' => [
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM,
				'textRotation' => 90,
			],
			'fill' => [
				'color' => ['argb' => '00cedaea'],
			],
		];
	}

	public function getStyleDisciplineCenter(){
		return [
			'alignment' => [
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				'textRotation' => 90,
			],
			'fill' => [
				'color' => ['argb' => '00cedaea'],
			],
		];
	}

	public function getStyleUserNames(){
		return [
			'fill' => [
				// 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'color' => ['argb' => '00cedaea'],
			],
		];
	}

	public function getStyleBold(){
		return [
			'font' => [
				'bold' => true,
			],
		];
	}

	public function getStyleDayDelimiter(){
		return [
			'borders' => [
				'right' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => ['argb' => '00004a80'],
				],
			],

		];
	}
//----------------------------------------------
	public function send($fileName, $emails) {
		Mail::to($emails)->send(new SendExcelMail($fileName));
		return 'ok';
	}
}
