<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ExamRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;

/**
 * Class ExamCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ExamCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Exam::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/exam');
        CRUD::setEntityNameStrings('exam', 'exams');
        $this->crud->enableExportButtons();
                        
        $user = Auth::user();
        $this->crud->addClause('where', 'company_id', '=', $user->company_id);
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
               'label' =>  'No',
               'name'  =>  'exam_no',
               'type'  =>  'number'
            ],
            [
               'label' => 'Category',
               'name'  => 'category_id',
               'type' => 'select',
               'entity' => 'category',
               'attribute' => 'category_name',
               'model' => 'App\Models\Category',
               'orderable' => false,
               'limit' => 150,
            ],
            [
               'label' =>  'Exam Name',
               'name'  =>  'exam_name',
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
        CRUD::setValidation(ExamRequest::class);

        // CRUD::setFromDb(); // fields

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
        CRUD::addFields([
            [
                'label'     => 'No',
                'name'      => 'exam_no',
                'type'      => 'number',
            ],
            [
                'label'     => 'Exam Name',
                'name'      => 'exam_name',
                'type'      => 'text',
            ],
            [
                'label'     => "Category",
                'type'      => 'select2',
                'name'      => 'category_id', // the db column for the foreign key

                // optional
                'entity'    => 'category', // the method that defines the relationship in your Model
                'model'     => "App\Models\Category", // foreign key model
                'attribute' => 'category_name', // foreign key attribute that is shown to user

                    // also optional
                'options'   => (function ($query) {
                        return $query->orderBy('category_name', 'ASC')->get();
                    }), // force the related options to be a custom query, instead of all(); you can use this to filter the results show in the select
            ],
            [
                'label'     => 'Rule',
                'name'      => 'exam_rule',
                'type'      => 'textarea'
            ],
            // [   // Table
            //     'name'            => 'questions',
            //     'label'           => 'Question',
            //     'type'            => 'table',
            //     'entity_singular' => 'question', // used on the "Add X" button
            //     // 'model'           => "App\Models\Question",
            //     'columns'         => [
            //         'question_no'     => 'No',
            //         'question_desc1'  => 'Description1',
            //         'question_desc2'  => 'Description2',
            //         'question_type'    => 'Type'
            //     ],
            //     'max' => 10, // maximum rows allowed in the table
            //     'min' => 0, // minimum rows allowed in the table
            // ],
            [   
                'name'            => 'questions',
                'label'           => 'Question',
                'type'            => 'repeatable',
                'fields'         => [
                    [
                        'name'     => 'question_no',
                        'label' => 'No',
                        'type'=> 'number',
                        // 'model'     => "App\Models\User",
                        // 'attribute' =>  'name',
                        'wrapperAttributes' => [
                            'class' => 'form-group col-md-2'
                          ],
                    ],
                    [
                        'name'     => 'question_desc1',
                        'label' => 'Q. Desc1',
                        'type'=> 'text',
                        // 'model'     => "App\Models\User",
                        // 'attribute' =>  'name',
                        'wrapperAttributes' => [
                            'class' => 'form-group col-md-4'
                          ],
                    ],
                    [
                        'name'     => 'question_desc2',
                        'label' => 'Q. Desc2',
                        'type'=> 'text',
                        // 'model'     => "App\Models\User",
                        // 'attribute' =>  'name',
                        'wrapperAttributes' => [
                            'class' => 'form-group col-md-4'
                          ],
                    ],
                    [
                        'name'     => 'question_type',
                        'label' => 'Q. Type',
                        'type'=> 'select_from_array',
                        'options' => [
                            'text'  => 'text',
                            'check' => 'check',
                            'radio' => 'radio'
                        ],
                        // 'model'     => "App\Models\Question",
                        // 'attribute' =>  'name',
                        'wrapperAttributes' => [
                            'class' => 'form-group col-md-2'
                          ],
                    ],
                    [   // CustomHTML
                        'name'  => 'separator',
                        'type'  => 'custom_html',
                        'value' => '<a href="{{$this->crud->setEditView(`backpack::crud.question`,3);}}" target="_blank">Go to question ></a>'
                    ],

                    
                    // [
                    //     ''
                    // ]
                ],
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
        $this->setupCreateOperation();
    }

    public function store(ExamRequest $request)
    {
        $input = $request->all();
        $option = json_decode($input['questions']);
        // print_r($option);
        $user = Auth::user();
        $exam = new Exam();
        $exam->exam_no = $input['exam_no'];
        $exam->category_id = $input['category_id'];
        $exam->exam_name = $input['exam_name'];
        $exam->exam_rule = $input['exam_rule'];
        $exam->user_id = $user->id;
        $exam->company_id = $user->company_id;
        $exam->save();

        if($option!=''){
            foreach($option as $options){
                $questions = new Question();
                $questions->question_no = $options->question_no;
                $questions->question_desc1 = $options->question_desc1;
                $questions->question_desc2 = $options->question_desc2;
                $questions->question_type = $options->question_type;
                $questions->exam_id = $exam->id;
                $questions->save();
            }
        }
 
        return redirect('exam');
    }   

    public function update(ExamRequest $request,  $id)
    {
        $input = $request->all();
        $option = json_decode($input['questions']);

        $exam = Exam::where('id',$id)->first();
        $exam->exam_no = $input['exam_no'];
        $exam->category_id = $input['category_id'];
        $exam->exam_rule = $input['exam_rule'];
        $exam->exam_name = $input['exam_name'];
        $exam->save();  

        Question::where('exam_id',$id)->delete();
        if($option!=''){
            foreach($option as $options){
                $questions = new Question();
                $questions->question_no = $options->question_no;
                $questions->question_desc1 = $options->question_desc1;
                $questions->question_desc2 = $options->question_desc2;
                $questions->question_type = $options->question_type;
                $questions->exam_id = $exam->id;
                $questions->save();
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
        return redirect('/exam');
    }

    public function edit($id)
    {
        $this->crud->hasAccessOrFail('update');
        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;
        $questions=Question::where('exam_id','=',$id)->get();
        $u=$this->crud->getUpdateFields();
        $u['questions']['value'] = json_encode($questions);
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
