<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\QuestionRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Database\QueryException;
use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
/**
 * Class QuestionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class QuestionCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Question::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/question');
        CRUD::setEntityNameStrings('question', 'questions');
        $this->crud->orderBy('exam_id', 'ASC');
        $this->crud->orderBy('question_no', 'ASC');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // CRUD::setFromDb(); // columns

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */

         CRUD::addColumns([
             [
                'label' =>  'Exam',
                'name'  =>  'exam_id',
                'type'  =>  'select',
                'entity' => 'exam',
                'attribute' => 'exam_name',
                'model' => 'App\Models\Exam',
             ],
             [
                'label' =>  'No',
                'name'  =>  'question_no',
                'type'  =>  'number',
             ],
             [
                'label' =>  'Desc1',
                'name'  =>  'question_desc1',
                'type'  =>  'text',
             ],
             [
                'label' =>  'Desc2',
                'name'  =>  'question_desc2',
                'type'  =>  'text',
             ],
             [
                'label' =>  'Type',
                'name'  =>  'question_type',
                'type'  =>  'text',
             ],
         ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(QuestionRequest::class);

        // CRUD::setFromDb(); // fields

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
        CRUD::field('question_no')->type('number');

        CRUD::addFields(
            [
                [
                    'label' =>  'Desc1',
                    'name'  =>  'question_desc1',
                    'type'  =>  'text'
                ],
                [
                    'label' =>  'Desc2',
                    'name'  =>  'question_desc2',
                    'type'  =>  'text'
                ],
                [
                    'label' =>  'Type',
                    'name'  =>  'question_type',
                    'type'=> 'select_from_array',
                    'options' => [
                        'text'  => 'text',
                        'check' => 'check',
                        'radio' => 'radio'
                    ],
                ],
                [   // Table
                    'name'            => 'answers',
                    'label'           => 'Answer',
                    'type'            => 'table',
                    'entity_singular' => 'answer', // used on the "Add X" button
                    // 'model'           => "App\Models\Question",
                    'columns'         => [
                        'answer_no'     => 'No',
                        'answer_desc1'  => 'Description1',
                        'answer_desc2'  => 'Description2',
                        'answer_val'  => 'Value'
                    ],
                    'max' => 4, // maximum rows allowed in the table
                    'min' => 0, // minimum rows allowed in the table
                ],
                
            ]);
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        // $this->setupCreateOperation();
        CRUD::setValidation(QuestionRequest::class);

        
        CRUD::field('question_no')->type('number');

        CRUD::addFields(
            [
                [
                    'label' =>  'Desc1',
                    'name'  =>  'question_desc1',
                    'type'  =>  'text'
                ],
                [
                    'label' =>  'Desc2',
                    'name'  =>  'question_desc2',
                    'type'  =>  'text'
                ],
                [
                    'label' =>  'Type',
                    'name'  =>  'question_type',
                    'type'=> 'select_from_array',
                    'options' => [
                        'text'  => 'text',
                        'check' => 'check',
                        'radio' => 'radio'
                    ],
                ],
                [   // Table
                    'name'            => 'answers',
                    'label'           => 'Answer',
                    'type'            => 'table',
                    'entity_singular' => 'answer', // used on the "Add X" button
                    // 'model'           => "App\Models\Question",
                    'columns'         => [
                        'answer_no'     => 'No',
                        'answer_desc1'  => 'Description1',
                        'answer_desc2'  => 'Description2',
                        'answer_val'    => 'Value',
                    ],
                    // 'value'          => 
                    'max' => 4, // maximum rows allowed in the table
                    'min' => 0, // minimum rows allowed in the table
                ],
                
            ]);
    }

    public function store(QuestionRequest $request)
    {
        $input = $request->all();
        $option = json_decode($input['answers']);

        $question = new Question();
        $question->question_no = $input['question_no'];
        // $question->category_id = $input['category_id'];
        $question->question_desc1 = $input['question_desc1'];
        $question->question_desc2 = $input['question_desc2'];
        $question->question_type = $input['question_type'];
        // $question->option_id = $input['option_id'];
        $question->save();

        if($option!=''){
            foreach($option as $options){
                $answers = new Answer();
                $answers->answer_no = $options->answer_no;
                $answers->answer_desc2 = $options->answer_desc1;
                $answers->answer_val = $options->answer_desc2;
                $answers->answer_val = $options->answer_val;
                $answers->question_id = $question->id;
                $answers->save();
            }
        }
 
        return redirect('question');
    }   

    public function update(QuestionRequest $request,  $id)
    {
        $input = $request->all();
        $option = json_decode($input['answers']);
        print_r($option);



        $question = Question::where('id',$id)->first();
        $question->question_no = $input['question_no'];
        // $question->category_id = $input['category_id'];
        $question->question_desc1 = $input['question_desc1'];
        $question->question_desc2 = $input['question_desc2'];
        $question->question_type = $input['question_type'];
        $question->save();  

        Answer::where('question_id',$id)->delete();

        if($option!=''){
            foreach($option as $options){
                $answer = new Answer();
                $answer->answer_no = $options->answer_no;
                $answer->answer_desc1 = $options->answer_desc1;
                $answer->answer_desc2 = $options->answer_desc2;
                $answer->answer_val = $options->answer_val;
                $answer->question_id = $question->id;
                $answer->save();
            }
        }
       
        // $answer = Answer::where('question_id',$id)->first();

        // $answer = new Answer();
        // $answer->answer_no = $answer_no;
        // $answer->answer_desc1 = $answer_desc1;
        // $answer->answer_desc2 = $answer_desc2;
        // $answer->answer_val = $answer_val;
        // $answer->question_id = $question->id;
        // $answer->save();/
        return redirect('question');
    }

    public function edit($id)
    {
        $this->crud->hasAccessOrFail('update');
        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;
        $answers=Answer::where('question_id','=',$id)->get();
        $u=$this->crud->getUpdateFields();
        $u['answers']['value'] = json_encode($answers);
        $this->crud->setOperationSetting('fields', $u);
        // get the info for that entry
        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.edit').' '.$this->crud->entity_name;

        $this->data['id'] = $id;


        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getEditView(),$this->data);
    }
}
