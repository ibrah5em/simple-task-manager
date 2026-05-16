<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $taskCount = $request->user()->tasks()->count();
        return view('dashboard', compact('taskCount'));
    }
}
