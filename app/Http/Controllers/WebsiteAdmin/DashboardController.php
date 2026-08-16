<?php

namespace App\Http\Controllers\WebsiteAdmin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'schools' => School::count(),
            'active_schools' => School::where('is_active', true)->count(),
            'school_admins' => User::where('role', UserRole::SchoolAdmin->value)->count(),
            'users' => User::whereNotNull('school_id')->count(),
        ];

        $recentSchools = School::latest()->limit(5)->get();

        return view('website-admin.dashboard', compact('stats', 'recentSchools'));
    }
}
