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
                $user = Company::all();
            }

            return $this->sendResponse($user, 'Success');
        } 
        else{ 
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
                        // ->join('answers','answers.question_id','=','questions.id')
                        ->select('questions.exam_id','questions.id as question_id','questions.question_desc1','questions.question_desc2','questions.question_type', 'questions.exam_id')
                        // 'answers.id as answer_id','answers.answer_desc1','answers.answer_desc2','answers.answer_val')
                        ->where('questions.exam_id','=',$exam_id)
                        ->get();

                $response = [];
                foreach($exam as $exams){

                    $answer = DB::table('answers')
                              ->select('answers.id as answer_id','answers.answer_desc1','answers.answer_desc2','answers.answer_val','answers.answer_no')
                              ->where('answers.question_id','=',$exams->question_id)
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

    public function updateResultJournal(Request $request){
        if(isset($_POST['email']) && isset($_POST['exam_id']) && isset($_POST['question_id'])){
            $email = $_POST['email'];
            $exam_id=$_POST['exam_id'];
            $question_id=$_POST['question_id'];
            $answer=$_POST['answer'];
            $result=$_POST['result'];
            $query = DB::table('users')
            ->select(DB::raw('COUNT(users.id) as totalemail'))
            ->where('email',$email)
            ->first();
            if($query->totalemail>=1){
                foreach($answer as $data){
                $update = DB::table('task_journal_answers')
                          ->join('task_journal_questions','task_journal_answers.hdr_qid','=','task_journal_questions.id')
                          ->join('task_journal_exams','task_journal_questions.hdr_id','=','task_journal_exams.id')
                          ->join('users','task_journal_exams.user_id','=','users.id')
                          ->where('users.email',$email)
                          ->where('task_journal_exams.exam_id',$exam_id)
                          ->where('task_journal_questions.question_id',$question_id)
                          ->where('task_journal_answers.answer_id',$data)
                          ->get();
                }            
            }
        
            echo $update;
        }else{ 
        return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }
    
}
