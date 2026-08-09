<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('school-admin.dashboard');
    }
}