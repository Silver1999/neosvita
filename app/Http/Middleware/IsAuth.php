<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class IsAuth{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
		$sessionUser = session()->get('user');

		if(!empty($sessionUser)){
			$logged = DB::table('logged')
				->where([
					'userID' => $sessionUser->id
				])
				->exists();

			if (!$logged){
				$request->session()->flush();
			}
		}

        return $next($request);
    }
}
