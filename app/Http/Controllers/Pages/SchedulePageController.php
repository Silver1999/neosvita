<?php

namespace App\Http\Controllers\Pages;

use App\DBHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SchedulePageController extends Controller {
	
	public function getPage(Request $request){
		$sessionUser = session()->get('user');

		if (empty($sessionUser)){
			if ($request->isMethod('get')){
				return redirect()->route('home');
			} else {
				return '';
			}
		}

		$rType = 'page'; //переменная отличает тип запроса (страница/контент)

		if(empty($sessionUser->position)){
			$permission = 1;
		} else {
			$permission = $sessionUser->position;
		}

		$timeSchedule = DBHelper::getTimeSchedule($sessionUser->institution);
		$weeks = DBHelper::getWeeksOfMonth();
		$currentWeek = $this->getCurrentWeek($weeks);
		$minWeek = $weeks->first();
		$maxWeek = $weeks->last();
		
		$vars = [
			'timeSchedule', 'weeks', 'maxWeek', 'minWeek', 'currentWeek', 
			'permission', 'institutionName', 'supGroups', 'rType'
		];

		$institutionName = $sessionUser->institutionName;
		$supGroups = [];
		
		if($sessionUser->position < 6) {
			if($sessionUser->position == 5){
				$supGroups = session()->get('groupIDs');
				if(count($supGroups)){
					$groupID = reset($supGroups);
				} 
			} else {
				$groupID = $sessionUser->group;
			}

			array_push($vars, 'schedule_sorted', 'groupID', 'supGroups');
			
			if(!empty($groupID)){
				$schedule_sorted = DBHelper::getSchedule($groupID, $currentWeek);
			}
		}

		if ($request->isMethod('post')) {
			return view('pages.schedule', compact($vars));
		} else {
			$pageView = 'schedule';
			$vars[] = 'pageView';
			return view('get', compact($vars));
		}
		
	}

	public function getSchedule(Request $request){
		$sessionUser = session()->get('user');
		if (empty($sessionUser)) return '';
		$position = $sessionUser->position;

		if(empty($position)){
			$permission = 1;
		} else {
			$permission = $position;
		}

		$arr = json_decode($request->getContent(), true);
		if(empty($arr['group']) || empty($arr['institution']) || empty($arr['week'])) return 'err';

		$groupID = DBHelper::getGroupID($arr['institution'], $arr['group']);

		if(!empty($groupID)) {
			$schedule_sorted = DBHelper::getSchedule($groupID, $arr['week']);

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
		} else {
			$schedule_sorted = null;
			$permission = 1;
		}

		if($arr['institution'] == $sessionUser->institution){
			$institutionName = $sessionUser->institutionName;
		} else {
			$institutionName = DBHelper::getInstitutionName($arr['institution']);
		}

		$timeSchedule = DBHelper::getTimeSchedule($sessionUser->institution);
		$weeks = DBHelper::getWeeksOfMonth();
		$currentWeek = $arr['week'];
		$minWeek = $weeks->first();
		$maxWeek = $weeks->last();
		
		return view('templates.schedule', compact('schedule_sorted', 'groupID', 'institutionName', 'permission', 'timeSchedule', 
												'currentWeek', 'minWeek', 'maxWeek'));
	}
	
	public function update(Request $request){
		$sessionUser = session()->get('user');
		if(empty($sessionUser->position)){
			$permission = 1;
		} else {
			$permission = $sessionUser->position;
		};

		if (empty($sessionUser) || $permission < 5) return '';

		$arr = json_decode($request->getContent(), true);

		if(!empty($arr)){
			DB::transaction(function () use ($arr, $sessionUser) {
				//расписание пар
				if(array_key_exists('group', $arr) && array_key_exists('schedule', $arr)){
					$groupID = $arr['group'];
					$schedule = $arr['schedule'];
					foreach($schedule as $day){
						DB::table('schedule')
						->updateOrInsert([
								'dayID' => $day['id'],
								'groupID' => $groupID,
							],[
								'content' => serialize($day['data'])
							]
						);
					}
				}
	
				//расписание продолжительности пар 
				if(array_key_exists('timeSchedule', $arr)){
					$timeSchedule = $arr['timeSchedule'];
					foreach($timeSchedule as $item){
						DB::table('time_schedule')
							->where([
								'id' => $item[0],
								'institutionID' => $sessionUser->institution
							])
							->update(['time' => $item[1]]);
					}
				}
			});
		}

		return "ok";
	}

	public function getCurrentWeek($weeks){
		if($weeks->count() > 2) {
			return $weeks->get(1);
		} else if($weeks->count() == 2) {
			if(date('j') < 14){
				return $weeks->get(0);
			} else {
				return $weeks->get(1);
			}
		} else {
			return $weeks->get(0);
		}
	}
}
