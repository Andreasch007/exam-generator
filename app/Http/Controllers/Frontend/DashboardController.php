<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;

class DashboardController 
{
    //
    public function index(){

        return view('frontend.dashboard');
    }
}
