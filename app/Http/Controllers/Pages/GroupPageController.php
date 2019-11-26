<?php

namespace App\Http\Controllers\Pages;

use App\DBHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DateTime;
use Illuminate\Support\Facades\DB;

class GroupPageController extends Controller {
	public function getPage(Request $request){
		$sessionUser = session()->get('user');
		if (empty($sessionUser)){
			if ($request->isMethod('get')){
				return redirect()->route('home');
			} else {
				return '';
			}
		}

		$position = $sessionUser->position;
		$rType = 'page'; //переменная отличает тип запроса (страница/контент)

		if (empty($position)) {
			$permission = 1;
		} else {
			$permission = $position;
		}

		$supGroups = [];
		$vars = [
			'institutionName' => $sessionUser->institutionName,
			'permission' => $permission,
			'rType' => $rType,
		];
		
		if($position < 6) {
			if($sessionUser->position == 5){
				$supGroups = session()->get('groupIDs');
				if(count($supGroups)){
					$groupID = reset($supGroups);
				} 
			} else {
				$groupID = $sessionUser->group;
			}
		}
		
		$res = [];
		if(!empty($groupID)){
			$vars['groupID'] = $groupID;
			$users = DBHelper::getGroupUsers($sessionUser->institution, $groupID);

			foreach($users as $user){
				$res[$user->position][] = $user;
			}
		}

		$vars['users'] = $res;
		$vars['supGroups'] = $supGroups;
		
		if ($request->isMethod('post')) {
			return view('pages.group', $vars);
		} else {
			$vars['pageView'] = 'group';
			return view('get', $vars);
		}
	}

	public function getGroup(Request $request){
		$sessionUser = session()->get('user');
		if (empty($sessionUser)) return '';
		$position = $sessionUser->position;

		if (empty($position)) {
			$permission = 1;
		} else {
			$permission = $position;
		}

		$arr = json_decode($request->getContent(), true);

		if(empty($arr['group']) || empty($arr['institution']) || mb_strlen($arr['group']) < 3) return 'err';

		$group = $arr['group'];
		$groupID = DBHelper::getGroupID($arr['institution'], $arr['group']);
		
		$users = collect([]);
		$res = [];

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

		if(!empty($groupID)) {
			$users = DBHelper::getGroupUsers($arr['institution'], $groupID);
			
			foreach($users as $user){
				$res[$user->position][] = $user;
			}
		}

		if($arr['institution'] == $sessionUser->institution){
			$institutionName = $sessionUser->institutionName;
		} else {
			$institutionName = DBHelper::getInstitutionName($arr['institution']);
		}

		return view('templates.group', [
			'groupID' => $groupID,
			'users' => $res,
			'group' => $group,
			'permission' => $permission,
			'institutionName' => $institutionName
		]);
	}
	
	public function update(Request $request){
		if (empty(session()->get('user')->position)) {
			$permission = 1;
		} else {
			$permission = session()->get('user')->position;
		};
		if (empty(session()->get('logged')) || $permission < 4) return '';

		$arr = json_decode($request->getContent(), true);

		if(!empty($arr)){
			DB::transaction(function () use ($arr) {
				foreach($arr as $user){
					$dob = new DateTime($user['dofb']);
					DB::table("users")
						->where([
							'id' => $user['id']
						])
						->update([
							'dayOfBirth' => $dob->format('Y-m-d'),
							'phone' => $user['phone'],
							'email' => $user['email'],
							'grant' => $user['grant'],

							'addressOfResidence' => $user['addressOfResidence'],
							'addressOfRegistration' => $user['addressOfRegistration'],
							'parentName1' => $user['parentName1'],
							'parentName2' => $user['parentName2'],
							'parentPhone' => $user['parentPhone'],
						]);
				}
			});
		}

		return "ok";
	}

	public function userRemove(Request $request){
		if (empty(session()->get('user')->position)) {
			$permission = 1;
		} else {
			$permission = session()->get('user')->position;
		};
		if (empty(session()->get('logged')) || $permission < 5) return '';
		
		$data = json_decode($request->getContent(), true);

		if(!empty($data['id'])){
			$res = 0;
			DB::transaction(function () use ($data, &$res) {
				$res = 	DB::table('users')
					->where([
						'id' => $data['id']
					])->delete();

				DB::table('logged')
					->where([
						'userID' => $data['id']
					])->delete();
			});

			if($res) {
				return 'ok';
			} else {
				return '!del';
			}
		} else {
			return 'err';
		}
	}

	public function changeGroupName(Request $request){
		if (empty(session()->get('user')->position)) {
			$permission = 1;
		} else {
			$permission = session()->get('user')->position;
		};
		if (empty(session()->get('logged')) || $permission < 4) return '';
		$data = json_decode($request->getContent(), true);

		if(!empty($data['id']) && !empty($data['newName']) && mb_strlen($data['newName']) > 3){
			DB::transaction(function () use ($data) {
				DB::table('groups')
				->where([
					'id' => $data['id']
				])
				->update([
					'name' => $data['newName'],
				]);
			});
			return 'ok';
		} else {
			return 'err';
		}
	}

	public function groupRemove(Request $request){
		$sessionUser = session()->get('user');
		if (empty($sessionUser)) return '';

		if (empty($sessionUser->position)) {
			$permission = 1;
		} else {
			$permission = $sessionUser->position;
		};

		if ($permission < 5) return '';

		$data = json_decode($request->getContent(), true);

		if(!empty($data['id'])){
			$res = 0;
			DB::transaction(function () use ($data, &$res) {
				$res = DBHelper::removeGroup($data['id']);
			});
			if($res) {
				if($sessionUser->position == 5){
					$groups = session()->get('groupIDs');
					if(count($groups) < 1) return 'reload';
					if(count($groups) == 1 && in_array($data['id'], $groups)) return 'reload';
				}
				return 'ok';
			} else {
				return '!del';
			}
		} else {
			return 'err';
		}
	}
	//-------------------------------------------------------

}
