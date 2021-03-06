<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CompanyRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\User;
use App\Models\UserApproval;
use DB;
/**
 * Class CompanyCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CompanyCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Company::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/company');
        CRUD::setEntityNameStrings('company', 'companies');
        $this->crud->addButtonFromModelFunction('top', 'open_approval', 'openApproval', 'start');    
        $user = Auth::user();
        $this->crud->addClause('where', 'id', '=', $user->company_id);
        $count = DB::table('companies')->select(DB::raw('COUNT(*) as counts'))->where('id',$user->company_id)->get();
        // dd($count);
        if($count[0]->counts>0){
            CRUD::denyAccess('create');
        }
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
        CRUD::addColumns(
            [
                [
                    'label'=> 'Company Name',
                    'name' => 'name', 
                    'type' => 'text'
                ],
            ]
        );
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(CompanyRequest::class);
        $user = Auth::user();
        // CRUD::setFromDb(); // fields

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
        CRUD::addFields([
            [
                'label'=> 'Company Name',
                'name' => 'name', 
                'type' => 'text'
            ],
            [
                'name' => 'user_id',
                'value'=> $user->id,
                'type' =>'hidden'
            ]
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

    public function store(CompanyRequest $request)
    {
        $user = Auth::user();
        $input = $request->all();

        $company = new Company();
        $company->name = $input['name'];
        $company->user_id = $user->id;
        $company->save();

        $userupdate = User::where('id','=',$user->id)
        ->update([
            'company_id'    =>  $company->id,
        ]);
		
		$userapproval = UserApproval::create([
			'user_id' => $user->id,
			'company_id' => $company->id,
            'approval' => 'Approved'
		]);
 
        return redirect('company');
    }   
}
