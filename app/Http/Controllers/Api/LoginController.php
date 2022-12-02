<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\apiBaseController;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class LoginController extends apiBaseController {
	public function login(Request $request) {

		$validator = Validator::make($request->all(), [
			'email' => 'required',
			'password' => 'required',
		]);

		if ($validator->fails()) {
			return response()->json(['error', $validator->fails()]);
		}

		$email = $request->email;

		$password = $request->password;

		// $user = User::where('email', $email)->first();

		$credentials = [

	        'email' => $email, 
	        'password' => $password

	    ];
	    
	    

	    if( auth()->attempt($credentials) ){ 
	      	$user = Auth::user(); 
	      	$user['role'] = $user->roles[0]->name;
		  	$user['token'] =  $this->get_user_token($user,"Laravel Personal Access Client");
	      	return $this->sendResponse('data',$user);
	    } else { 
			return response()->json(['error'=>'Unauthorised'], 401);
	    } 

		/*if (empty($user)) {

			return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');
		} elseif (!\Hash::check($password, $user->password)) {

			return $this->sendError('အချက်အလက် များ မှားယွင်း နေပါသည်။');

		} else {

			$user = User::where('email', $email)->first();

			$id = $user->id;
			$email = $user->email;
			$name = $user->name;

			$role = $user->roles[0]->name;

            //return $this->sendResponse('user',[$user,$role]);

			if ($user->hasRole('owner')) {

				return response()->json([
					'user' => [
						'id' => $user->id,
						'email' => $user->email,
						'name' => $user->name,
						'role' => $role,
					],
					'success' => true,
					'message' => "successful",
				]);

			}
		}*/
	}
}