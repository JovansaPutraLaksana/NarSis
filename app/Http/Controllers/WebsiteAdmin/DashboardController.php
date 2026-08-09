<?php

namespace App\Http\Controllers\WebsiteAdmin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('website-admin.dashboard');
    }
}