<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;

class RegisterController extends BaseController
{
    //
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        
        $input = $request->all();
        $query = DB::table('users')
                 ->select(DB::raw('COUNT(users.id) as totalemail'))
                 ->where('email',$input['email'])
                 ->first();
        $input['password'] = bcrypt($input['password']);//hash("sha256", $input['password']);
        
        if($query->totalemail>=1){
            return $this->sendError('Unauthorised',['error' => 'User already existed']);
        } else {
            $user = User::create($input);
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            $success['name'] =  $user->name;
       
            return $this->sendResponse($success, 'User register successfully.');
        }
        }
        
     
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password]) && isset($_POST['playerID']) && isset($_POST['uid'])){ 
            $user = Auth::user(); 
            $playerID = $_POST['playerID'];
            $device_id = $_POST['uid'];
            $success['token'] =  $user->createToken('MyApp')->accessToken; 
            $success['name'] =  $user->name;
            $success['email'] = $user->email;
            $success['company_id'] = $user->company_id;


            $update = DB::table('users')
                      ->where('users.email',$user->email)
                      ->update([
                          'player_id' => $playerID,
                          'uid'       => $device_id  
                      ]);
   
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }
}
