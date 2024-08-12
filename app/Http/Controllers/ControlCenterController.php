<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ControlCenterController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'user.id']);
        $this->middleware(['permission:view_control_center']);
    }

    public function index(Request $request)
    {
        return view('control_center.index');
    }
}
