<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TaskHeaderRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use DB;
use Redirect;
use App\Models\TaskHeader;
use App\Models\TaskDetail;
use Illuminate\Support\Facades\Validator;
/**
 * Class TaskHeaderCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TaskHeaderCrudController extends CrudController
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
        CRUD::setModel(\App\Models\TaskHeader::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/taskheader');
        CRUD::setEntityNameStrings('task', 'task');
        $this->crud->addButtonFromModelFunction('line', 'generate_task', 'buttonGenerate', 'beginning');
    }

    public function generateTransaction($id){
        $generate=DB::statement('CALL generate_transaction(?)',[$id]);

        return $generate;
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
                 'label'    =>  'Date',
                 'name'     =>  'doc_date',
                 'type'     =>  'date'
             ],
             [
                 'label'    =>  'Start Time',
                 'name'     =>  'start_time',
                 'type'     =>  'time'
             ],
             [
                 'label'    =>  'Task',
                 'name'     =>  'exam_id',
                 'entity'   =>  'exam',
                 'attribute' =>  'exam_name',
                 'type'     =>  'select',
             ]
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
        CRUD::setValidation(TaskHeaderRequest::class);

        // CRUD::setFromDb(); // fields

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
        CRUD::addFields([
            [
                'label'     =>  'Date',
                'name'      =>  'doc_date',
                'type'      =>  'date'
            ],
            [
                'label'     =>  'Start Time',
                'name'      =>  'start_time',
                'type'      =>  'time'
            ],
            [
                'label'     =>  'Exam',
                'name'      =>  'exam_id',
                'type'      =>  'select2',
                'entity'    =>  'exam',
                'attribute' =>  'exam_name', 
                'model'     => "App\Models\Exam",
                'options'   => (function ($query) {
                    $user = Auth::user();
                    return $query->orderBy('exam_no', 'ASC')->where('company_id', $user->company_id)->get();
                }), 
            ],
            [   
                'name'            => 'taskdetails',
                'label'           => 'User',
                'type'            => 'repeatable',
                'fields'         => [
                    [
                        'name'     => 'user_id',
                        'label' => 'User',
                        'type'=> 'select2',
                        'model'     => "App\Models\User",
                        'attribute' =>  'name',
                        // 'wrapperAttributes' => [
                        //     'class' => 'form-group col-md-8'
                        //   ],
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

    public function store(TaskHeaderRequest $request)
    {
        $input = $request->all();
        $option = json_decode($input['taskdetails']);

        $taskheader = new TaskHeader();
        $taskheader->doc_date = $input['doc_date'];
        $taskheader->start_time = $input['start_time'];
        $taskheader->exam_id = $input['exam_id'];
        $taskheader->save();

        if($option!=''){
            foreach($option as $options){
                Validator::make((array)$options, ['user_id' => 'required'])->validate();
                $taskdetail = new TaskDetail();
                $taskdetail->header_id = $taskheader->id;
                $taskdetail->user_id = $options->user_id;
                $taskdetail->save();
            }
        }
 
        return redirect('admin/taskheader');
    }   

    public function update(taskHeaderRequest $request,  $id)
    {
        $input = $request->all();
        $option = json_decode($input['taskdetails']);

        $taskheader = TaskHeader::where('id',$id)->first();
        $taskheader->doc_date = $input['doc_date'];
        $taskheader->start_time = $input['start_time'];
        $taskheader->exam_id = $input['exam_id'];
        $taskheader->save();  

        TaskDetail::where('header_id',$id)->delete();

        if($option!=''){
            foreach($option as $options){
                Validator::make((array)$options, ['user_id' => 'required'])->validate();
                $taskdetail = new TaskDetail();
                $taskdetail->header_id = $id;
                $taskdetail->user_id = $options->user_id;
                $taskdetail->save();
            }
        }
        return redirect('admin/taskheader');
    }

    public function edit($id)
    {
        $this->crud->hasAccessOrFail('update');
        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;
        $taskdetail=TaskDetail::where('header_id','=',$id)->get();
        $u=$this->crud->getUpdateFields();
        $u['taskdetails']['value'] = json_encode($taskdetail);
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
