<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TaskResultRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Auth;

/**
 * Class TaskResultCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TaskResultCrudController extends CrudController
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
        CRUD::setRoute(config('backpack.base.route_prefix') . '/taskresult');
        CRUD::setEntityNameStrings('taskresult', 'task_results');
        CRUD::denyAccess(['delete','update','create']);
        CRUD::enableExportButtons();
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
                'label'    =>  'Extern No',
                'name'     =>  'extern_no',
                'type'     =>  'text',
                'limit'    => 50
            ],
            [
                'label'    =>  'Date',
                'name'     =>  'created_at',
                'type'  =>  'date',
                'searchlable'   => true
            ],
            [
                'label'    =>  'Exam',
                'name'     =>  'exam_name',
                'type'     =>  'text',
                'limit'    => 2000
            ],
            [
                'label'    =>  'Question',
                'name'     =>  'question_desc1',
                'type'     =>  'text',
                'limit'    => 2000
            ],
            [
                'label'    =>  'Answer Value',
                'name'     =>  'answer_val',
                'type'     =>  'text',
                'limit'    => 2000
            ],
            [
                'label'    =>  'Answer Result',
                'name'     =>  'result',
                'type'     =>  'text',
                'limit'    => 2000
            ]
            // [
            //     'label'    =>  'UID',
            //     'name'     =>  'UID',
            //     'type'     =>  'select',
            //     'entity'   =>  'talent',
            //     'attribute' => 'uid',
            //     'model'     => 'App\Models\User',
            // ],
            // [
            //     'label'    =>  'Link',
            //     'name'     =>  'link',
            //     'type'     =>  'text'
            // ],
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
        CRUD::setValidation(TaskResultRequest::class);

        CRUD::setFromDb(); // fields

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
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

    public function search()
    {
        $user = Auth::user();
        $this->crud->hasAccessOrFail('list');

        $this->crud->applyUnappliedFilters();

        $this->crud->query->join('task_journal_exams','task_trans_headers.id','=','task_journal_exams.header_id')
        ->join('exams','task_journal_exams.exam_id','exams.id')
        ->join('task_journal_questions','task_journal_exams.id','=','task_journal_questions.hdr_id')
        ->join('questions','task_journal_questions.question_id','questions.id')
        ->join('task_journal_answers','task_journal_questions.id','=','task_journal_answers.hdr_qid')
        ->join('answers','task_journal_answers.answer_id','answers.id')
        ->select('task_trans_headers.extern_no', 'exams.exam_name','task_journal_exams.created_at','questions.question_desc1','answers.answer_val','task_journal_answers.result')
        ->where('task_trans_headers.company_id',$user->company_id)
        ->where('task_journal_answers.result','!=',null)
        ->get();
   

        $totalRows = $this->crud->model->join('task_journal_exams','task_trans_headers.id','=','task_journal_exams.header_id')->count();
        $filteredRows = $this->crud->query->toBase()->getCountForPagination();
        $startIndex = request()->input('start') ?: 0;
        // if a search term was present
        if (request()->input('search') && request()->input('search')['value']) {
            // filter the results accordingly
            $this->crud->applySearchTerm(request()->input('search')['value']);
            // recalculate the number of filtered rows
            $filteredRows = $this->crud->count();
        }
        // start the results according to the datatables pagination
        if (request()->input('start')) {
            $this->crud->skip((int) request()->input('start'));
        }
        // limit the number of results according to the datatables pagination
        if (request()->input('length')) {
            $this->crud->take((int) request()->input('length'));
        }
        // overwrite any order set in the setup() method with the datatables order
        if (request()->input('order')) {
            // clear any past orderBy rules
            $this->crud->query->getQuery()->orders = null;
            foreach ((array) request()->input('order') as $order) {
                $column_number = (int) $order['column'];
                $column_direction = (strtolower((string) $order['dir']) == 'asc' ? 'ASC' : 'DESC');
                $column = $this->crud->findColumnById($column_number);
                if ($column['tableColumn']) {
                    // apply the current orderBy rules
                    $this->crud->orderByWithPrefix($column['name'], $column_direction);
                }

                // check for custom order logic in the column definition
                if (isset($column['orderLogic'])) {
                    $this->crud->customOrderBy($column, $column_direction);
                }
            }
        }

        // show newest items first, by default (if no order has been set for the primary column)
        // if there was no order set, this will be the only one
        // if there was an order set, this will be the last one (after all others were applied)
        // Note to self: `toBase()` returns also the orders contained in global scopes, while `getQuery()` don't.
        $orderBy = $this->crud->query->toBase()->orders;
        $table = $this->crud->model->getTable();
        $key = $this->crud->model->getKeyName();

        $hasOrderByPrimaryKey = collect($orderBy)->some(function ($item) use ($key, $table) {
            return (isset($item['column']) && $item['column'] === $key)
                || (isset($item['sql']) && str_contains($item['sql'], "$table.$key"));
        });

        if (! $hasOrderByPrimaryKey) {
            $this->crud->orderByWithPrefix($this->crud->model->getKeyName(), 'DESC');
        }

        $entries = $this->crud->getEntries();

        return $this->crud->getEntriesAsJsonForDatatables($entries, $totalRows, $filteredRows, $startIndex);
    }
}
