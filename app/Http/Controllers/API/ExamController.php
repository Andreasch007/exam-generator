<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Category;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\BaseController as BaseController;
use DB;

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
                $query2 = DB::table('users')
                    ->select('company_id')
                    ->where('email',$email)
                    ->first();
                if ($query2->company_id==null)
                {
                    $company = Company::all();
                }
                else
                {
                    $company = DB::table('companies')
                    ->join('users','companies.id','=','users.company_id')
                    ->select('companies.name as company_name','users.id as user_id','companies.id as company_id, users.name as user_name')
                    ->where('users.email',$email)
                    ->first();

                    $response = [];
                    foreach($company as $companies){
                        $response = [
                            'company_id'    => $companies->company_id,
                            'company_name'  => $companies->company_name,
                            'user_id'       => $companies->user_id,
                            'user_name'     => $companies->user_name
                        ];
                    }
                }
                return $this->sendResponse($response, 'Success');
             }
            else{ 
                return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
            } 
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
                        ->select('categories.*','exams.*','task_journal_exams.doc_date','task_journal_exams.start_time')
                        ->where('users.email','=',$email)
                        ->orderBy('task_journal_exams.doc_date')
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
                        ->select('questions.exam_id','questions.id as question_id','questions.question_desc1','questions.question_desc2','questions.question_type', 'questions.exam_id')
                        // 'answers.id as answer_id','answers.answer_desc1','answers.answer_desc2','answers.answer_val')
                        ->where('questions.exam_id','=',$exam_id)
                        ->where('users.email',$email)
                        ->orderBy('task_journal_questions.idx')
                        ->get();

                $response = [];
                foreach($exam as $exams){

                    $answer = DB::table('answers')
                              ->join('task_journal_answers','answers.id','task_journal_answers.answer_id')
                              ->select('answers.id as answer_id','answers.answer_desc1','answers.answer_desc2','answers.answer_val','answers.answer_no')
                              ->where('answers.question_id','=',$exams->question_id)
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
        if(isset($_POST['email']) && isset($_POST['company_id'])){
            $email = $_POST['email'];
            $company_id = $_POST['company_id'];
            $query = DB::table('users')
            ->select(DB::raw('COUNT(users.id) as totalemail'))
            ->where('email',$email)
            ->first();
            if($query->totalemail>=1)
            {
                $update = DB::table('users')
                ->where('email',$email)
                -update([
                    'company_id'=>$company_id
                ]);
            }
            return $this->sendResponse($update, 'Success');
        } else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }

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
    
}
