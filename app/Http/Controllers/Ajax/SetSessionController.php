<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SetSessionController extends Controller
{
    public function index($_key, $_value)
    {
        session([$_key => $_value]);
        return "done";
    }
}
