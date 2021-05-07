<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Category;
use App\Models\User;
use App\Models\Company;
use App\Models\UserApproval;
use App\Models\TaskHeader;
use App\Models\TaskDetail;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\BaseController as BaseController;
use DB;
// use Illuminate\Support\Facades\Password;
use Mail;
use Validator;
use Carbon;
// use Message;

class ExamController extends BaseController
{
    //
    public function getCategory(Request $request){
        if(isset($_POST['email'])){ 
            $user = Auth::user();
            $email = $_POST['email'];
            $query = DB::table('users')
                    ->select(DB::raw('COUNT(users.id) as totalemail'))
                    ->where('email',$email)
                    ->first();
            if($query->totalemail>=1){
                $category = DB::table('categories')
                            ->join('exams','categories.id','=','exams.category_id')
                            ->join('companies','exams.company_id','=','companies.id')
                            ->join('users','companies.id','=','users.company_id')
                            ->select('categories.*')
                            ->where('users.email','=',$email)
                            ->get();
            }

            return $this->sendResponse($category, 'Success');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }

    public function getProfile(Request $request){
        if(isset($_POST['email'])){ 
            $user = Auth::user();
            $email = $_POST['email'];
            $query = DB::table('users')
                    ->select(DB::raw('COUNT(users.id) as totalemail'))
                    ->where('email',$email)
                    ->first();
            if($query->totalemail>=1){
                $user = User::select('users.*')
                ->where('email',$email)
                ->first();
            }

            return $this->sendResponse($user, 'Success');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }

    public function getCompany(Request $request){
        if(isset($_POST['email'])){ 
            $user = Auth::user();
            $email = $_POST['email'];
            $query = DB::table('users')
                    ->select(DB::raw('COUNT(users.id) as totalemail'))
                    ->where('email',$email)
                    ->first();
            if($query->totalemail>=1){

                $company = DB::table('companies')
                           ->select('companies.*')
                           ->distinct()
                           ->get();
       
                return $this->sendResponse($company, 'Success');
             }
            else{ 
                return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
            } 
        }
    }
	
	public function unfollowCompany(Request $request){
        if(isset($_POST['email']) && isset($_POST['company_id'])){
            $email = $_POST['email'];
            $company_id = $_POST['company_id'];
            $query = DB::table('users')
                    ->select(DB::raw('COUNT(users.id) as totalemail'),'id','company_id')
                    ->where('email',$email)
                    ->groupBy('id','company_id')
                    ->first();
            $check = DB::table('user_approvals')
                    ->select(DB::raw('COUNT(id) as totalapproval'))
                    ->where('user_id',$query->id)
                    ->where('company_id',$company_id)
                    //  ->groupBy('id','company_id')
                    ->first();
            if($query->totalemail>=1){
                if($check->totalapproval!=0){
                    $delete = UserApproval::destroy([
                                'company_id'=>$company_id,
                                'user_id'=>$query->id
                            ]);
                    $response = array("error" => true);
                    $response["error"] = FALSE;
                    $response["message"] = "Request Success !";
                
                    return json_encode($response); 
                }else{
                    $response = array("error" => FALSE);
                    $response["error"] = TRUE;
                    $response["message"] = "You havent follow this company!";

                    return json_encode($response);
                }
                
            }
            return $this->sendResponse($delete, 'Success');
        }else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }
           

    public function sendApproval(Request $request){
        if(isset($_POST['email']) && isset($_POST['company_id'])){
            $email = $_POST['email'];
            $company_id = $_POST['company_id'];
            $query = DB::table('users')
                    ->select(DB::raw('COUNT(users.id) as totalemail'),'id','company_id')
                    ->where('email',$email)
                    ->groupBy('id','company_id')
                    ->first();
            $check = DB::table('user_approvals')
                     ->select(DB::raw('COUNT(id) as totalapproval'))
                     ->where('user_id',$query->id)
                     ->where('company_id',$company_id)
                    //  ->groupBy('id','company_id')
                     ->first();
            if($query->totalemail>=1){
                    if($check->totalapproval==0){
                        $update = UserApproval::create([
                                    'company_id'=>$company_id,
                                    'user_id'=>$query->id
                                ]);
                        $response = array("error" => true);
                        $response["error"] = FALSE;
                        $response["message"] = "Request Success !";
                    
                        return json_encode($response); 
                    }
                    else{
                        $response = array("error" => FALSE);
                        $response["error"] = TRUE;
                        $response["message"] = "You have already requested to follow for this company";
    
                        return json_encode($response);
                    }
                
            }
            return $this->sendResponse($update, 'Success');
        }else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }

    public function getUserApproval(){
        if(isset($_POST['email'])){
            $email = $_POST['email'];
            $query = DB::table('users')
            ->select(DB::raw('COUNT(users.id) as totalemail'))
            ->where('email',$email)
            ->first();
            if($query->totalemail>=1){
                $userapproval = DB::table('user_approvals')
                                ->join('companies','user_approvals.company_id','companies.id')
                                ->join('users','user_approvals.user_id','users.id')
                                ->select('users.id','companies.name','user_approvals.approval')
                                ->where('users.email',$email)
                                ->get();
            }
            return $this->sendResponse($userapproval, 'Success');
        }else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }
        

    public function getExam(Request $request){
        if(isset($_POST['email'])){
            $user = Auth::user();
            $email = $_POST['email'];
            $query = DB::table('users')
                    ->select(DB::raw('COUNT(users.id) as totalemail'))
                    ->where('email',$email)
                    ->first();
            if($query->totalemail>=1){
                
                $exam = DB::table('exams')
                        ->join('task_journal_exams','exams.id','task_journal_exams.exam_id')
                        ->join('users','task_journal_exams.user_id','=','users.id')
                        ->join('categories','exams.category_id','categories.id')
                        ->join('task_journal_questions','task_journal_exams.id','task_journal_questions.hdr_id')
                        ->select('categories.category_name','exams.exam_name','exams.id','task_journal_exams.doc_date',
                        'task_journal_exams.start_time','task_journal_exams.end_time',DB::raw('COUNT(task_journal_questions.id) as jml'),DB::raw('TIMESTAMPDIFF(MINUTE,task_journal_exams.start_time,task_journal_exams.end_time) as waktu'), 
                        'exams.exam_rule', 'task_journal_exams.flag_done')
                        ->where('users.email','=',$email)
                        ->groupBy('categories.category_name','exams.exam_name','exams.id', 'task_journal_exams.doc_date','task_journal_exams.start_time','task_journal_exams.end_time','exams.exam_rule','task_journal_exams.flag_done')
                        ->orderBy('task_journal_exams.start_time', 'DESC')
                        ->get();
            }
            return $this->sendResponse($exam, 'Success');
        }else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
        
    }

    public function getQuestion(Request $request){
        if(isset($_POST['email']) && isset($_POST['exam_id'])){
            $user = Auth::user();
            $email = $_POST['email'];
            $exam_id=$_POST['exam_id'];
            $query = DB::table('users')
                    ->select(DB::raw('COUNT(users.id) as totalemail'))
                    ->where('email',$email)
                    ->first();
            if($query->totalemail>=1){
                $exam = DB::table('questions')
                        ->join('task_journal_questions','questions.id','task_journal_questions.question_id')
                        // ->join('answers','answers.question_id','=','questions.id')
                        ->join('task_journal_exams','task_journal_questions.hdr_id','=','task_journal_exams.id')
                        ->join('users','task_journal_exams.user_id','=','users.id')
                        ->select('questions.exam_id','questions.id as question_id','questions.question_desc1','questions.question_desc2','questions.question_type')
                        // 'answers.id as answer_id','answers.answer_desc1','answers.answer_desc2','answers.answer_val')
                        ->where('questions.exam_id','=',$exam_id)
                        ->where('users.email',$email)
                        ->orderBy('task_journal_questions.idx')
                        ->get();

                $response = [];
                foreach($exam as $exams){

                    $answer = DB::table('answers')
                              ->join('task_journal_answers','answers.id','task_journal_answers.answer_id')
                              ->join('task_journal_questions','task_journal_answers.hdr_qid','task_journal_questions.id')
                              ->join('task_journal_exams','task_journal_questions.hdr_id','=','task_journal_exams.id')
                              ->join('users','task_journal_exams.user_id','=','users.id')
                              ->select('answers.id as answer_id','answers.answer_desc1','answers.answer_desc2','answers.answer_no')
                              ->where('answers.question_id','=',$exams->question_id)
                              ->where('users.email',$email)
                              ->orderBy('task_journal_answers.idx')
                              ->get();
                    $response[] = [
                        'question_id'=>$exams->question_id,
                        'exam_id'   => $exams->exam_id,
                        'question_desc1'=>$exams->question_desc1,
                        'question_desc2'=>$exams->question_desc2,
                        'question_type'=>$exams->question_type,
                        'answer'=>$answer
                    ];
                    
                }
            }
            return json_encode($response,200);
        }else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
        
    }

    public function updateCompany(Request $request){
        if(isset($_POST['email'])){
            $email = $_POST['email'];
            $name = $_POST['name'];
            $query = DB::table('users')
            ->select(DB::raw('COUNT(users.id) as totalemail'))
            ->where('email',$email)
            ->first();
            if($query->totalemail>=1)
            {
                $update = DB::table('users')
                ->where('email',$email)
                ->update([
                    'name'=>$name
                ]);
            }
            return $this->sendResponse($update, 'Success');
        } else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }

    public function getExamRule(Request $request){
        if(isset($_POST['email']) && isset($_POST['exam_id'])){
            $email = $_POST['email'];
            $exam_id=$_POST['exam_id'];
            $query = DB::table('users')
                    ->select(DB::raw('COUNT(users.id) as totalemail'))
                    ->where('email',$email)
                    ->first();
            if($query->totalemail>=1){
                $examrule = DB::table('exams')
                            ->join('users','exams.company_id','=','users.company_id')
                            ->select("exams.exam_rule")
                            ->where('users.email','=',$email)
                            ->where('exams.id',$exam_id)
                            ->get();
            }
            return $this->sendResponse($examrule, 'Success');
         }else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }
    
    public function forgotPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        $input=$request->all();
        $query = DB::table('users')
        ->select(DB::raw('COUNT(users.id) as totalemail'))
        ->where('email',$input['email'])
        ->first();
        if($query->totalemail>=1)
            {   
                $query2 = DB::table('users')
                 ->select('name')
                 ->where('email',$input['email'])
                 ->first();
                
                $new_password = Str::random(7);       
                $data = array($query2->name);
                Mail::send([],[], function($message) use($input, $new_password) {
                    
                    $message->to($input['email'])
                            ->subject('Your New Password ')
                            ->setBody(
                                '<html><h3>This is your New Password = </h3>
                                <br><bold> '.$new_password.' </bold>
                                <br><br><bold> Please remember your password AND Do Not Share Your Password
                                to Anyone !!! </bold></html>','text/html'
                            );
                    $message->from(env('MAIL_USERNAME','victoriussaputra@gmail.com'),'Victorius');
                });
                $update = DB::table('users')
                ->where('email',$input['email'])
                ->update([
                            'password' => bcrypt($new_password)
                        ]);
                return $this->sendResponse($update, 'Message Sent. Please Check Your Email');
            }
        else
        { 
            return $this->sendError('Email tidak terdaftar.', ['error'=>'Unauthorised']);
        }
                    
    }

    // public function changePassword(Request $request){
    //     if(isset($_POST['email']) && isset($_POST['password'])){
    //         $email = $_POST['email'];
    //         $password = $_POST['password'];
    //         $input = $request->all();
    //         $userid = Auth::guard('api')->user()->id;
    //         // $rules = array(
    //         // 'old_password' => 'required',
    //         // 'new_password' => 'required|min:6',
    //         // 'confirm_password' => 'required|same:new_password',
    //         // );
    //         // $validator = Validator::make($input, $rules);
    //         $query = DB::table('users')
    //             ->select(DB::raw('COUNT(users.id) as totalemail'))
    //             ->where('email',$email)
    //             ->first();
    //         if($query->totalemail>=1)
    //         {
    //             if ($validator->fails()) {
    //                 $arr = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());
    //             } else {
    //                 try {
    //                     if ((Hash::check(request('old_password'), Auth::user()->password)) == false) {
    //                         $arr = array("status" => 400, "message" => "Check your old password.", "data" => array());
    //                     } else if ((Hash::check(request('new_password'), Auth::user()->password)) == true) {
    //                         $arr = array("status" => 400, "message" => "Please enter a password which is not similar then current password.", "data" => array());
    //                     } else {
    //                         User::where('id', $userid)->update(['password' => Hash::make($input['new_password'])]);
    //                         $update = DB::table('users')
    //                         ->where('email',$email)
    //                         ->update([
    //                             'password'=>$password,
    //                         ]);
    //                         $arr = array("status" => 200, "message" => "Password updated successfully.", "data" => array());
    //                     }
    //                 } catch (\Exception $ex) {
    //                     if (isset($ex->errorInfo[2])) {
    //                         $msg = $ex->errorInfo[2];
    //                     } else {
    //                         $msg = $ex->getMessage();
    //                     }
    //                     $arr = array("status" => 400, "message" => $msg, "data" => array());
    //                 }
    //             }
    //             return \Response::json($arr);
    //         }
    //         else{ 
    //             return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
    //         }
    //     }
    // }
    public function updateResultJournal(Request $request){
        if(isset($_POST['email']) && isset($_POST['exam_id']) && isset($_POST['question_id'])){
            $email = $_POST['email'];
            $exam_id=$_POST['exam_id'];
            $question_id=$_POST['question_id'];
            $question_type=$_POST['question_type'];
            $answers=$_POST['answer'];
            $results=$_POST['result'];
            $query = DB::table('users')
            ->select(DB::raw('COUNT(users.id) as totalemail'))
            ->where('email',$email)
            ->first();
            if($query->totalemail>=1){
                if($question_type=='check'){
                    foreach($answers as $data){
                        foreach($results as $result){
                            $update = DB::table('task_journal_answers')
                            ->join('task_journal_questions','task_journal_answers.hdr_qid','=','task_journal_questions.id')
                            ->join('task_journal_exams','task_journal_questions.hdr_id','=','task_journal_exams.id')
                            ->join('users','task_journal_exams.user_id','=','users.id')
                            ->where('users.email',$email)
                            ->where('task_journal_exams.exam_id',$exam_id)
                            ->where('task_journal_questions.question_id',$question_id)
                            ->where('task_journal_answers.answer_id',$data)
                            ->update([
                                'result'=>$result
                            ]);
                        }
                    }
                }else {
                    $update = DB::table('task_journal_answers')
                    ->join('task_journal_questions','task_journal_answers.hdr_qid','=','task_journal_questions.id')
                    ->join('task_journal_exams','task_journal_questions.hdr_id','=','task_journal_exams.id')
                    ->join('users','task_journal_exams.user_id','=','users.id')
                    ->where('users.email',$email)
                    ->where('task_journal_exams.exam_id',$exam_id)
                    ->where('task_journal_questions.question_id',$question_id)
                    ->where('task_journal_answers.answer_id',$answers)
                    ->update([
                        'result'=>$results
                    ]);
                }            
            }
            
            return $this->sendResponse($update, 'Success');
        }else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }

    public function createTask(){
        if(isset($_POST['exam_no']) && isset($_POST['uid']) && isset($_POST['start_time']) && isset($_POST['end_time']) && isset($_POST['company_id'])){
            $exam_no = $_POST['exam_no'];
            $start_time = $_POST['start_time']; 
            $end_time = $_POST['end_time'];
            $uid = json_decode($_POST['uid'],true);
            $company_id=$_POST['company_id'];

            $examID = Exam::select('id')->where('exam_no',$exam_no)->first();

            foreach($uid as $uids){
               $user[]=DB::table('users')->select('id')->where('uid',$uids)->first();
            }
       
            foreach($user as $users){
                if($users->id!=null){
                    $taskheader = TaskHeader::create([
                        'start_time' => $start_time,
                        'end_time'  => $end_time,
                        'exam_id' => $examID->id,
                        'company_id' => $company_id
                        // 'doc_date' => Carbon::now()
                    ]);
    
                    $taskdetail = TaskDetail::create([
                        'user_id' => $users->id,
                        'header_id' => $taskheader->id
                    ]);
                    
                }
            }  
           
            $response = array("error" => true);
            $response["error"] = FALSE;
            $response["message"] = "Success Create Task!";
        
            return json_encode($response); 
        } else {
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }
    
}
