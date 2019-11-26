<?php

namespace App\Http\Controllers\Pages;

use App\DBHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class ListPageController extends Controller {

	public function getPage(Request $request){
		$sessionUser = session()->get('user');
		if (empty($sessionUser)){
			if ($request->isMethod('get')){
				return redirect()->route('home');
			} else {
				return '';
			}
		}
		
		if(empty($sessionUser->position)){
			$permission = 1;
		} else {
			$permission = $sessionUser->position;
		};

		$institutionName = $sessionUser->institutionName;
		$supGroups = [];
		$rType = 'page'; //переменная отличает тип запроса (страница/контент)

		if($sessionUser->position < 6) {
			$arr['period'] = -1;
			$arr['group'] = $sessionUser->groupName;
			$arr['institution'] = $sessionUser->institution;

			if($sessionUser->position == 5){
				$supGroups = session()->get('groupIDs');
				if(count($supGroups)){
					$groupID = reset($supGroups);
				} 
			} else {
				$groupID = $sessionUser->group;
			}

			$vars = ['institutionName', 'arr', 'permission', 'supGroups', 'rType'];

			if(!empty($groupID)){
				//get weeks
				$firstDay = date('N', mktime(0, 0, 0, date('n'), 1, date('Y'))) == 7 ? 2 : 1;
				$weeks = DB::table('days')
					->where([
						'year' => date('Y'),
						'month' => date('n'),
					])
					->whereIn('day', [$firstDay, date('j'), date('t')])
					->limit(3)
					->pluck('week');
				//-------------------------

				//определение недели
				$minWeek = $weeks->first();
				$maxWeek = $weeks->last();

				if($weeks->count() > 2) {
					$currentWeek = $weeks->get(1);
				} else if($weeks->count() == 2) {
					if(date('j') < 14){
						$currentWeek = $weeks->get(0);
					} else {
						$currentWeek = $weeks->get(1);
					}
				} else {
					$currentWeek = $weeks->get(0);
				}

				$arr['currentWeek'] = $currentWeek; // для экспорта

				//дни выбранной недели
				$days = DBHelper::getListWeekDays($currentWeek, $groupID);

				// ученики и директор (без заведующего и куратора)
				$users = DBHelper::getListUsers($groupID);

				$userIDs = [];
				foreach($users as $key => $user){
					$userIDs[] = $user->id;
				}

				$dayIDs = [];
				foreach($days as $day){
					$dayIDs[] = $day->id;
				}

				$skips_sorted = DBHelper::getListSkipsWeek($userIDs, $dayIDs);

				unset($userIDs, $dayIDs);

				$arr['groupID'] = $groupID;
				session()->put("export", [
					'type'=> 'week',
					'data' => $arr,
				]);

				array_push($vars, 'groupID', 'days', 'users', 'skips_sorted', 'weeks', 'currentWeek', 'minWeek', 'maxWeek');
			}

			if ($request->isMethod('post')) {
				return view('pages.list', compact($vars));
			} else {
				$vars[] = 'pageView';
				$pageView = 'list';
				return view('get', compact($vars));
			}
		}

		if ($request->isMethod('post')) {
			return view('pages.list', compact('institutionName', 'rType', 'supGroups'));
		} else {
			$pageView = 'list';
			return view('get', compact('institutionName', 'pageView', 'rType', 'supGroups'));
		}
	}

	public function getWeekTable(Request $request){
		$sessionUser = session()->get('user');
		if (empty($sessionUser)){
			if ($request->isMethod('get')){
				return redirect()->route('home');
			} else {
				return '';
			}
		}

		$position = $sessionUser->position;

		if(empty($position)){
			$permission = 1;
		} else {
			$permission = $position;
		}

		$arr = json_decode($request->getContent(), true);
		
		$groupID = DBHelper::getGroupID($arr['institution'], $arr['group']);

		//название уч. заведения
		if($arr['institution'] == $sessionUser->institution){
			$institutionName = $sessionUser->institutionName;
		} else {
			$institutionName = DBHelper::getInstitutionName($arr['institution']);
		}

		//определение чужой группы
		if($position < 5) {
			if($sessionUser->group != $groupID){
				$permission = 1;
			}
		} else if($position == 5){
			if(!in_array($groupID, session()->get('groupIDs'))){
				$permission = 1;
			}
		} else if($position == 6){
			if($sessionUser->institution != $arr['institution']){
				$permission = 1;
			}
		}

		$users = collect([]);

		if(!empty($groupID)){
			//get weeks
			$firstDay = date('N', mktime(0, 0, 0, date('n'), 1, date('Y'))) == 7 ? 2 : 1;
			$weeks = DB::table('days')
				->where([
					'year' => date('Y'),
					'month' => date('n'),
				])
				->whereIn('day', [$firstDay, date('j'), date('t')])
				->limit(3)
				->pluck('week');
			//-------------------------

			//если не выбрана неделя, то выдаётся текущая
			$minWeek = $weeks->first();
			$maxWeek = $weeks->last();

			if($arr['period'] < 1) {
				if($weeks->count() > 2) {
					$currentWeek = $weeks->get(1);
				} else if($weeks->count() == 2) {
					if(date('j') < 14){
						$currentWeek = $weeks->get(0);
					} else {
						$currentWeek = $weeks->get(1);
					}
				} else {
					$currentWeek = $weeks->get(0);
				}
			} else {
				$currentWeek = $arr['period'];
			}

			$arr['currentWeek'] = $currentWeek; // для экспорта

			//дни выбранной недели
			$days = DBHelper::getListWeekDays($currentWeek, $groupID);

			// ученики и директор (без заведующего и куратора)
			$users = DBHelper::getListUsers($groupID);

			$userIDs = [];
			foreach($users as $key => $user){
				$userIDs[] = $user->id;
			}

			$dayIDs = [];
			foreach($days as $day){
				$dayIDs[] = $day->id;
			}

			$skips_sorted = DBHelper::getListSkipsWeek($userIDs, $dayIDs);

			unset($userIDs, $dayIDs);

			$arr['groupID'] = $groupID;
			session()->put("export", [
				'type'=> 'week',
				'data' => $arr,
			]);

			return view('templates.tableWeek', compact(
				'groupID', 'days', 'institutionName', 'users', 'skips_sorted', 
				'weeks', 'currentWeek', 'minWeek', 'maxWeek', 'arr', 'permission'
			));
		} else {
			return view("templates.groupNotFound");
		}
	}
	public function getMonthTable(Request $request){
		$sessionUser = session()->get('user');
		if(empty($sessionUser)) return '';
		if(empty($sessionUser->position)){
			$permission = 1;
		} else {
			$permission = $sessionUser->position;
		};

		$arr = json_decode($request->getContent(), true);

		$arr['month'] = $arr['period'] < 1 ? date('n') : $arr['period'];

		//выбираем месяцы одного учебного года
		if($arr['month'] < 9 ){
			if(date('n') >= 9){
				$arr['year'] = date('Y') + 1;
			} else {
				$arr['year'] = date('Y');
			}
		} else {
			if(date('n') >= 9){
				$arr['year'] = date('Y');
			} else {
				$arr['year'] = date('Y') - 1;
			}
		}

		$groupID = DBHelper::getGroupID($arr['institution'], $arr['group']);

		if($arr['institution'] == $sessionUser->institution){
			$institutionName = $sessionUser->institutionName;
		} else {
			$institutionName = DBHelper::getInstitutionName($arr['institution']);
		}
		$users = collect([]);

		if(!empty($groupID)){
			//дни
			$days = DB::table('days')
			->where([
				'year' => $arr['year'],
				'month' => $arr['month'],
				['DofW', '<', 7],
			])
			->get();

			$days_sorted = [];
			foreach($days as $day){
				$days_sorted[$day->week][] = $day;
			}
			$dayShift[0] = $days->first()->DofW - 1;
			$dayShift[1] = 6 - $days->last()->DofW;

			// пользователи
			$users = DBHelper::getListUsers($groupID);

			$userIDs = [];
			foreach($users as $key => $user){
				$userIDs[] = $user->id;
			}

			$skips_sorted = DBHelper::getListSkipsMonth($userIDs, $days->first()->id, $days->last()->id);

			unset($days, $userIDs);

			$arr['groupID'] = $groupID;
			session()->put("export", [
				'type'=> 'month',
				'data' => $arr,
			]);
			
			return view('templates.tableMonth', compact(
				'groupID', 'days_sorted', 'institutionName', 'users', 'skips_sorted', 'dayShift', 'arr', 'permission'
			));
		} else {
			return view("templates.groupNotFound");
		}
	}
	public function getYearTable(Request $request){
		$sessionUser = session()->get('user');
		if(empty($sessionUser)) return '';
		if(empty($sessionUser->position)){
			$permission = 1;
		} else {
			$permission = $sessionUser->position;
		};

		$arr = json_decode($request->getContent(), true);

		//выбираем месяцы одного учебного года
		switch($arr['period']){
			case 1: 
				$arr['month'] = [9, 12];
				if(date('n') >= 9){
					$arr['year'] = date('Y');
				} else {
					$arr['year'] = date('Y') - 1;
				}
				break;
			case 2: 
				$arr['month'] = [1, 6];
				if(date('n') >= 9){
					$arr['year'] = date('Y') + 1;
				} else {
					$arr['year'] = date('Y');
				}
				break;
			default:
				$arr['year'] = date('Y');
				if(date('n') < 9){
					$arr['month'] = [1, 6];
				} else {
					$arr['month'] = [9, 12];
				}
				break;
		}

		$groupID = DBHelper::getGroupID($arr['institution'], $arr['group']);

		if($arr['institution'] == $sessionUser->institution){
			$institutionName = $sessionUser->institutionName;
		} else {
			$institutionName = DBHelper::getInstitutionName($arr['institution']);
		}
		$users = collect([]);

		if(!empty($groupID)){
			//дни
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

			// пользователи
			$users = DBHelper::getListUsers($groupID);

			$userIDs = [];
			foreach($users as $key => $user){
				$userIDs[] = $user->id;
			}

			$skips_sorted = DBHelper::getListSkipsYear($userIDs, $days->first()->week, $days->last()->week);

			unset($userIDs);
			$arr['groupID'] = $groupID;
			session()->put("export", [
				'type'=> 'year',
				'data' => $arr,
			]);
			
			return view('templates.tableYear', compact(
				'groupID', 'days_sorted', 'institutionName', 'users', 'skips_sorted', 'dayShift', 'arr', 'permission'
			));
		} else {
			return view("templates.groupNotFound");
		}
	}
	public function tableUpdate(Request $request){
		$sessionUser = session()->get('user');
		if(empty($sessionUser->position)){
			$permission = 1;
		} else {
			$permission = $sessionUser->position;
		};

		if (empty($sessionUser) || $permission < 2) return '';

		$arr = json_decode($request->getContent(), true);

		if(!empty($arr["skips"])){
			DB::transaction(function () use ($arr) {
				foreach($arr["skips"] as $skip){
					$pp = 0;
					$bp = 0;
					foreach($skip['status'] as $sk){
						if($sk == 2) $pp++;
						else if($sk == 3) $bp++;
					}
					DB::table('skips')
						->updateOrInsert([
							'dayID' => $skip["day"], 
							'userID' => $skip["user"], 
							],[
							'week' => $skip["week"], 
							'status' => serialize($skip["status"]),
							'pp' => $pp,
							'bp' => $bp,
							]
						);
				}
			});
		}
		
		return 'ok';
	}
}
