<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\LoginMaster;
use App\Models\RolePermission;
use App\Models\SavedEstimate;
use Illuminate\Http\Request;
use Auth;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = LoginMaster::getUser();
        session()->put('user', $user);
        return view('layouts.dashboard');
    }
}
