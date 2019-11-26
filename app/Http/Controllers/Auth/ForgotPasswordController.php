<?php

namespace App\Http\Controllers\Auth;

use App\Functions;
use App\Mail\SendRestoreCode;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Request;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    // use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
	}
	
	public function restore(Request $request){
		$messages = [
			'required' => 'Всі поля незаповнені.',
			'email' => 'Невірний формат E-mail.',
			'min' => 'Мінімальна кількість символів для поля :attribute : :min.',
		];

		$validator = Validator::make($request->all(), [
			'email' => 'bail|required|min:3|email',
			'pass' =>  'bail|required|min:6',
			'code' =>  'bail|required|min:3',
		], $messages);

		if ($validator->fails()) {
			$errors = implode('<br>', $validator->errors()->all());

			return [
				'status' => 'err',
				'message' => $errors
			];
		} else {
			$restore = DB::table('restores')
				->where([
					'email' => $request->input('email')
				])
				->limit(1)
				->get();

			if($restore->isEmpty()){
				return [
					'status' => 'err',
					'message' => 'Невірний E-mail або код.'
				];
			}

			$restoreCode = trim($request->input("code"));
			$restore = $restore->first();

			if($restoreCode != $restore->restoreCode){
				return [
					'status' => 'err',
					'message' => 'Невірний E-mail або код.'
				];
			}

			$result = DB::table('users')
				->where([
					'id' => $restore->userID
				])
				->update([
					'password' => Hash::make($request->input('pass'))
				]);

			if(!$result) {
				return [
					'status' => 'err',
					'message' => 'Невірний E-mail або код.'
				];
			}

			$result = DB::table('restores')
				->where([
					'userID' => $restore->userID
				])
				->delete();

			if($result) {
				return [
					'status' => 'ok',
					'message' => "Пароль змінено."
				];
			}
		}
	}

	public function sendRestore(Request $request){
		$validator = Validator::make($request->all(), [
			'email' => 'bail|required|min:3|email',
		]);

		if ($validator->fails()) {
			return 'err';
		} else {
			$email = $request->input('email');
			$userID = DB::table('users')
				->where([
					'email' => $email,
				])
				->limit(1)
				->pluck('id');

			if(!$userID->isEmpty()) {
				$restoreCode = Functions::codeGen(12);
				Mail::to($email)->send(new SendRestoreCode($restoreCode));
	
				DB::table('restores')
					->where([
						'userID' => $userID->first(),
					])
					->updateOrInsert([
						'email' => $email,
						'userID' => $userID->first(),
					],[
						'restoreCode' => $restoreCode,
						'time' => time(),
					]
				);
			}
			// в любом случае "Ok" -> защита от подбора email
			return 'ok';
		}
	}
}
