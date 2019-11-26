<?php

namespace App;

use Illuminate\Support\Facades\DB;

class DBHelper {

	public static function getGroupID($institution, $groupName){
		return DB::table('groups')
			->where([
				'institutionID' => $institution,
				'name' => $groupName
			])
			->limit(1)
			->value('id');
	}

	public static function getGroupIDs($institution, $groupNames){
		return DB::table('groups')
			->select('id', 'name')
			->where([
				'institutionID' => $institution,
			])
			->whereIn('name', $groupNames)
			->get();
	}

	public static function getSuperintendentGroups($userID){
		$groupIDs = DB::table('superintendent_groups')
			->select('superintendent_groups.groupID', 'groups.name')
			->where([
				'userID' => $userID,
			])
			->leftJoin('groups', 'superintendent_groups.groupID', '=', 'groups.id')
			->get();

		$groups_sorted = [];
		foreach($groupIDs as $group){
			$groups_sorted[$group->name] = $group->groupID;
		}
		
		return $groups_sorted;
	}

	public static function getInstitutionName($institution){
		return DB::table('institutions')
				->where([
					'id' => $institution
				])
				->value('name');
	}

	public static function getDirectorName($institution){
		$director = DB::table('users')
			->select('name', 'surname', 'patronymic')
			->where([
				'position' => 6,
				'institution' => $institution,
			])
			->limit(1)
			->get();

		if(empty($director)){
			return $directorName ='';
		} else {
			$director = $director->get(0);
			return $director->surname . ' ' . $director->name . ' ' . $director->patronymic;
		}
	}

	public static function getSchedule($groupID, $week){
		$schedule = DB::table('days')
			->where([
				'week' => $week,
				// 'month' => date('n'),
				// 'week' => 10,
				// 'month' => 11,
				['DofW', '<', '7'],
			])
			->leftJoin('schedule', function($join) use ($groupID) {
				$join->on('days.id', '=', 'schedule.dayID')
				->where(['schedule.groupID' => $groupID]);
			})
			->select('days.id', 'days.DofW', 'schedule.content')
			->orderBy('days.id', 'asc')
			->limit(6)
			->get();

		$schedule_sorted = [];
		foreach($schedule as $day){
			$schedule_sorted[$day->DofW] = $day;
		}

		return $schedule_sorted;
	}

	public static function getTimeSchedule($institution){
		return DB::table("time_schedule")
			->where(['institutionID' => $institution])
			->limit(24)
			->get();
	}

	public static function getWeeksOfMonth(){
		$firstDay = date('N', mktime(0, 0, 0, date('n'), 1, date('Y'))) == 7 ? 2 : 1;
		return DB::table('days')
			->where([
				'year' => date('Y'),
				'month' => date('n'),
			])
			->whereIn('day', [$firstDay, date('j'), date('t')])
			->limit(3)
			->pluck('week');
	}

	//только на странице группы
	public static function getGroupUsers($institution, $groupID){
		$users = DB::table('users')
			->select('id', 'name', 'surname', 'patronymic', 'position',
					'dayOfBirth', 'phone', 'email', 'grant',
					'addressOfResidence', 'addressOfRegistration', 'parentName1', 'parentName2', 'parentPhone')
			->where([
				'group' => $groupID,
			])
			->orWhere(function($query) use ($institution) {
				$query->where([
					'group' => 0,
					'institution' => $institution,
					'position' => 5,
				]);
			})
			->get();

		$supUsers = DB::table('superintendent_groups')
			->where([
				'groupID' => $groupID,
			])
			->pluck('userID')
			->toArray();

		$users = $users->reject(function ($user, $key) use ($supUsers) {
			return $user->position == 5 && !in_array($user->id, $supUsers);
		});

		return $users;
	}

	public static function removeGroup($groupID){
		DB::transaction(function () use ($groupID) {
			DB::table('groups')
				->where([
					'id' => $groupID
				])
				->delete();

			//получение зав. связанных с группой
			$superintendents = DB::table('superintendent_groups')
				->whereIn('userID', function($query) use ($groupID) {
					$query
					->select('userID')
					->from('superintendent_groups')
					->where([
						'groupID' => $groupID
					]);
				})
				->pluck('userID');
				
			//на удаление зав. если приписана только одна группа
			$filtered = $superintendents->countBy()->filter(function ($value, $key) {
				return $value < 2;
			})->keys();

			DB::table('superintendent_groups')
				->where([
					'groupID' => $groupID
				])
				->delete();

			$userIDs = DB::table('users')
				->where([
					'group' => $groupID
				])
				->pluck('id');

			$allUsers = $userIDs->concat($filtered);

			if(!$allUsers->isEmpty()){
				DB::table('users')
					->whereIn('id', $allUsers)
					->delete();

				DB::table('logged')
					->whereIn('userID', $allUsers)
					->delete();
			}
			
			DB::table('skips')
				->whereIn('userID', $userIDs)
				->delete();

			DB::table('schedule')
				->where([
					'groupID' => $groupID
				])
				->delete();

		});
		
		return true;
	}

	public static function getListWeekDays($currentWeek, $groupID){
		$discipleNames = DB::table('schedule')
			->select('dayID', 'content')
			->where([
				'groupID' => $groupID
			]);

		return DB::table('days')
			->select('days.*', 'content')
			->where([
				'week' => $currentWeek,
				['DofW', '<', 7]
			])
			->orderBy('days.id', 'asc')
			->leftJoinSub($discipleNames, 'day_id', function($join) {
				$join->on('dayID', '=', 'id');
				})
			->get();
	}

	//ученики и директор (без заведующего и куратора)
	public static function getListUsers($groupID){
		return DB::table('users')
			->select('id', 'name', 'surname', 'patronymic', 'position')
			->where([
				'group' => $groupID,
				['position', '<', 4]
			])
			->orderBy('surname', 'asc')
			->get();
	}

	public static function getListSkipsWeek($userIDs, $dayIDs){
		$skips_sorted = [];
		if(empty($userIDs) || empty($dayIDs)) return $skips_sorted;

		$skips = DB::table('skips')
			->whereIn('dayID', $dayIDs)
			->whereIn('userID', $userIDs)
			->get();

		foreach($skips as $skip){
			$skips_sorted[$skip->userID][$skip->dayID] = $skip->status;
		}

		return $skips_sorted;
	}

	public static function getListSkipsMonth($userIDs, $firstDay, $lastDay){
		$skips_sorted = [];
		if(empty($userIDs)) return $skips_sorted;

		$skips = DB::table('skips')
			->select('dayID', 'userID', 'pp', 'bp')
			->whereIn('userID', $userIDs)
			->whereBetween('dayID', [$firstDay, $lastDay])
			->get();
		
		foreach($skips as $skip){
			$skips_sorted[$skip->userID][$skip->dayID] = $skip;
		}
		return $skips_sorted;
	}

	public static function getListSkipsYear($userIDs, $firstWeek, $lastWeek){
		$skips_sorted = [];
		if(empty($userIDs)) return $skips_sorted;

		$skips = DB::table('skips')
			->select('userID', 'week', DB::raw('SUM(pp) as pp'), DB::raw('SUM(bp) as bp'))
			->whereIn('userID', $userIDs)
			->whereBetween('week', [$firstWeek, $lastWeek])
			->groupBy('userID', 'week')
			->distinct()
			->get();

		foreach($skips as $skip){
			$skips_sorted[$skip->userID][$skip->week] = $skip;
		}

		return $skips_sorted;
	}

}
