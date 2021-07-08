<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use DB;
use Mail;

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
            $response = array("error" => FALSE);
            $response["error"] = TRUE;
            $response["message"] = 'User already existed';

            return json_encode($response);
        } else {
            $user = User::create($input);
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            $success['name'] =  $user->name;
       		
            Mail::send([],[], function($message) use($input) {
                $email = $input['email'];
                $message->to($input['email'])
                        ->subject('Please Verify Your Email ')
                        ->setBody(
                            '<html><h2>Please click button below to verify your email</h2>
                            <br> 
							<a href="{{route(`verify`,[`email` => Crypt::encrypt("`.$input[`email`].`")])}}">Verify Here</a>
                            </html>','text/html'
                        );
                $message->from(env('MAIL_USERNAME','nocortech@metamorphz.com'),'Exam-Generator');
            });
            return $this->sendResponse($success, 'Please verify your email to login');
        }
     }
        
     
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    	{
		 $val = $request->only(['email', 'password']);
         if(Auth::attempt($val) && isset($_POST['playerID']) && isset($_POST['uid'])){ 
            $user = Auth::user();
			$is_verif = User::select('is_verif')->where('email',$user->email)->first();
            $playerID = $_POST['playerID'];
            $device_id = $_POST['uid'];
			 
			if($is_verif->is_verif==1){
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
			}else {
				$response = array("error" => FALSE);
				$response["error"] = TRUE;
				$response["message"] = "Incorrect Email or Password or You haven't been verified!";

				return json_encode($response);
            // return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
			}
        } 
        
    }
	
	public function updateVerification($email){
            ///if(isset($_GET['email'])){
				/// $email = $_GET['email'];
				$email = Crypt::decrypt($email);
				$query = DB::table('users')
				->select(DB::raw('COUNT(users.id) as totalemail'))
				->where('email',$email)
				->first();
				if($query->totalemail>=1)
				{
					$update = DB::table('users')
					->where('email',$email)
					->update([
						'is_verif'=> 1
					]); 
					return view('verified');
				}
			
			else{
				  return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
				}
				
     }
}