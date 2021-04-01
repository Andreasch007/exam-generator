<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use DB;

class TaskHeader extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'task_trans_headers';
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
    public function buttonGenerate($crud = false)
    {
        // return '<a class="btn btn-sm btn-link" href="http://google.com?q='.urlencode($this->text).'" data-toggle="tooltip" title="Just a demo custom button."><i class="las la-search"></i> Google it</a>';
        return '<a class="btn btn-sm btn-warning" id="btn-gen" type="button">Generate Task</a>
        <script>
        $("#btn-gen").click(function() {
            $.ajax({
                type: "POST",
                url: "https://exam.graylite.com/admin/generate/'.$this->id.'",
                dataType: "json",
            }).done(function(){
                alert("Success Generate");
            }); 
        });
        </script>';
    }

    // public function generateTask($id){
    //     dd($id);
    // }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function exam(){
        return $this->belongsTo(Exam::class);
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
