<?php

namespace App\Http\Controllers\ParentUser;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $parent = auth()->user()->parentProfile;
        $children = $parent->students()->with('user')->get()->sortBy('user.name');

        return view('parent.dashboard', compact('parent', 'children'));
    }
}
