<?php

namespace App\Http\Controllers\Auth;

use App\DBHelper;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->middleware('guest')->except('logout');
	}
	
	public function login(Request $request){
		$validator = Validator::make($request->all(), [
			'email' => 'bail|required|min:3|email',
			'pass' => 'required|min:6',
		]);

		if ($validator->fails()) {
			$errors = $validator->errors();
			
			$response = [
				'status' => 'err',
				'email' => $errors->has('email'),
				'pass' => $errors->has('pass'),
			];
			return $response;
		}

		//текущий юзер
		$query = DB::table('users')
			->select('users.id', 'users.password', 'users.name', 'users.position', 'users.institution', 'users.group', 
					 'groups.name as groupName', 'institutions.name as institutionName')
			->leftJoin('groups', 'users.group', '=', 'groups.id')
			->leftJoin('institutions', 'users.institution', '=', 'institutions.id')
			->where('email', '=', $request->input("email"))
			->first();
		
		if(empty($query)){
			return $response = [
				'status' => 'err',
				'email' => false,
				'pass' => false,
			];
		}

		if(Hash::check($request->input("pass"), $query->password)) {
			session()->regenerate();
			session()->put("logged", $query->name);
			session()->put("remember", $request->input("remember") == 'on');
			session()->put("user", $query);

			if($query->position == 5){
				$groupIDs = DBHelper::getSuperintendentGroups($query->id);
				session()->put("groupIDs", $groupIDs);
			}

			DB::table('logged')
				->updateOrInsert([
					'userID' => $query->id
				],[]);

			return $response = [
				'status' => 'ok',
			];
		} else {
			
			return $response = [
				'status' => 'err',
				'email' => false,
				'pass' => false,
			];
		}
	}

	public function logout(Request $request){
		DB::table('logged')
			->where([
				'userID' => session()->get('user')->id
			])
			->delete();

		$request->session()->flush();
		return 'ok';
	}
}
