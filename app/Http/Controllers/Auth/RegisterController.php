<?php

namespace App\Http\Controllers\Auth;

use App\DBHelper;
use App\Functions;
use App\User;
use App\Http\Controllers\Controller;
use DateTime;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data){
        // return Validator::make($data, [
        //     'name' => ['required', 'string', 'max:255'],
        //     'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        //     'password' => ['required', 'string', 'min:8', 'confirmed'],
		// ]);
		return true;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data){
        // return User::create([
        //     'name' => $data['name'],
        //     'email' => $data['email'],
        //     'password' => Hash::make($data['password']),
		// ]);
		return true;
	}
	
	public function register(Request $request){
		$messages = [
			'required' => 'Всі поля незаповнені.',
			'email' => 'Невірний формат email.',
			'email.unique' => 'E-mail вже використовується іншим користувачем.',
			'min' => 'Мінімальна кількість символів для поля :attribute : :min.',
			'date_format' => 'Невірний формат дати.',
		];

		$validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'surname' => 'required|min:3',
            'patronymic' => 'required|min:3',
			'dob' => 'required|date_format:d-m-Y',
			
            'sex' => 'required|integer|min:1',
            'country' => 'required|integer|min:1',
            'city' => 'required|integer|min:1',
            'institution' => 'required|integer|min:1',
			'role' => 'required|integer|min:1',
			
            'group' => 'required|min:3',
            'email' => 'bail|required|min:3|email|unique:users',
            'pass' => 'required|min:6',
            'code' => 'required|min:3',
		], $messages);

		
        if ($validator->fails()) {
			$errors = implode('<br>', $validator->errors()->all());

			$response = [
				'status' => 'err',
				'message' => $errors
			];
            return $response;
		}

		if(trim($request->input("code")) != "iDZPkf0^l*!8iHS8&@vL"){
			return [
				'status' => 'err',
				'message' => 'Неправильний код доступу.'
			];
		}

		DB::transaction(function () use ($request) {
			$dob = new DateTime($request->input("dob"));

			if($request->input("role") > 4){
				$groupID = 0;
			} else {
				$group = Functions::cleanGroupsString($request->input("group"), true);
				$groupID = DBHelper::getGroupID($request->input("institution"), $group);
				
				if(is_null($groupID)) {
					$groupID = DB::table("groups")
						->insertGetId([
							'name' => $group[0],
							'institutionID' => $request->input("institution"),
						]);
				}
			}
	
			$user = User::create([
				'name' => $request->input("name"),
				'surname' => $request->input("surname"),
				'patronymic' => $request->input("patronymic"),
				'dayOfBirth' => $dob->format('Y-m-d'),
				'sex' => $request->input("sex"),
				'country' => $request->input("country"),
				'city' => $request->input("city"),
				'institution' => $request->input("institution"),
				'position' => $request->input("role"),
				'group' => $groupID,
				'email' => $request->input("email"),
				'password' => Hash::make($request->input('pass')),
				// 'codeID' => $request->input("code"),
				'codeID' => 1,
			]);

			if($request->input("role") == 5){
				$groups = Functions::cleanGroupsString($request->input("group"));
				$groupIDs = DBHelper::getGroupIDs($request->input("institution"), $groups);

				$inserts = [];
				foreach($groupIDs as $group){
					$inserts[] = ['userID' => $user->id, 'groupID' => $group->id];
					unset($groups[array_search($group->name, $groups)]);
				}

				foreach($groups as $group){
					$groupID = DB::table("groups")
						->insertGetId([
							'name' => $group,
							'institutionID' => $request->input("institution"),
						]);

						$inserts[] = ['userID' => $user->id, 'groupID' => $groupID];
				}
	
				DB::table('superintendent_groups')->insertOrIgnore($inserts);
			}
		});


		return [
			'status' => 'ok',
			'message' => 'Вітаємо! <br>
			Реєстрація пройшла успішно!<br>
			Тепер у Вас є можливість<br>
			входу в нашу систему.<br>
			Ми цінуємо те що Ви з нами!<br>'
		];
	}

}
