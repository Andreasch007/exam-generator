<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use DB;

class UserApproval extends Model
{   
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'user_approvals';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function changeColor()
    {       
    //     $data = DB::table('companies')
    //     ->select('approval')
    //     ->get();
        
    // foreach ($data as $datas)
    //     {
            if($this->approval == 'Approved')
            {
                return "<span class='badge' style='background-color:green; color: white;'>".$this->approval."</span>";
            }
            else 
            {
                return "<span class='badge' style='background-color:orange; color: white;'>".$this->approval."</span>";
            }
        // echo $data;

        
         //return '<span style="color:red">'.$this->approval.'<span>'>'.';
        // }
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function company(){
        return $this->belongsTo(Company::class);
    }




    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}