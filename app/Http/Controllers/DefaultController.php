<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class DefaultController extends Controller
{
    public function index()
    {
        // if(!sess)
        return redirect("/Dashboard");
    }

    public function check_session(){
        
    }

}
