<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserApprovalRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Auth;


/**
 * Class TagCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class UserApprovalController extends CrudController
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
        CRUD::setModel(\App\Models\UserApproval::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/UserApproval');
        CRUD::setEntityNameStrings('UserApproval', 'User Approval');
       
        CRUD::denyAccess('create');
        $user = Auth::user();
        $this->crud->addClause('where', 'company_id', '=', $user->company_id);
        $this->crud->addClause('where', 'company_id', '!=', 0);

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
        // CRUD::setFromDb(); // columns
        CRUD::addColumns([
            [
                'label' => 'User',
                'name'  => 'name',
                'entity'   =>  'user',
                'attribute'=>  'name',
                'model' => 'App\Models\User',
                'type'  => 'select',
                'limit' => 150,
             ],
             [
                 'label' => 'Company',
                 'name'  => 'company_id',
                 'entity'   =>  'company',
                 'attribute'=>  'name',
                 'model' => 'App\Models\Company',
                 'type'  =>  'select',
                 'limit' => 150,
             ],
            [
                'label' => 'Status',
                'name'  => 'approval',
                'type' => 'model_function',
                'function_name' => 'changeColor',
                'limit' => 150,
                'default' => 'Need Approval'
            ],  

        ]);
        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(UserApprovalRequest::class);

        // CRUD::setFromDb(); // fields
        CRUD::addFields([
            [
                'label' => 'User',
                'name'  => 'user_id',
                'type'  =>  'select',
                'entity'   =>  'user',
                'attribute'=>  'name',
                'model' => 'App\Models\User',
                'attributes' => [
                 'readonly'  =>  'readonly'
                 ],
                'limit' => 150,
             ],
            [
               'label' => 'Company',
               'name'  => 'company_id',
               'type'  =>  'select',
               'entity'   =>  'company',
               'attribute'=>  'name',
               'model' => 'App\Models\Company',
               'attributes' => [
                'readonly'  =>  'readonly'
                ],
               'limit' => 150,
            ],
            [
               'label' => 'Approval',
               'name'  => 'approval',
               'type' => 'enum',
               'option' => [
                   'Need Approval' => "Need Approval",
                   'Approved' => "Approved"
               ],
               'default' => 'Need Approval'
            ]
       ]);

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


}
