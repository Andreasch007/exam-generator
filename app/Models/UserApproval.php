<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\User;

class UserApproval extends Model
{   
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'companies';
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
                return "<span class='badge' style='background-color:green'>".$this->approval."</span>";
            }
            else 
            {
                return "<span class='badge' style='background-color:orange'>".$this->approval."</span>";
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
        return $this->belongsTo(User::class,'id','company_id');
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