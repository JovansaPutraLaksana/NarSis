<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $schoolId = auth()->user()->school_id;

        $stats = [
            'teachers' => User::where('school_id', $schoolId)->where('role', UserRole::Teacher->value)->where('is_active', true)->count(),
            'students' => User::where('school_id', $schoolId)->where('role', UserRole::Student->value)->where('is_active', true)->count(),
            'parents' => User::where('school_id', $schoolId)->where('role', UserRole::Parent->value)->where('is_active', true)->count(),
            'classes' => SchoolClass::where('is_active', true)->count(),
            'subjects' => Subject::where('is_active', true)->count(),
        ];

        $activeYear = AcademicYear::with('semesters')->where('is_active', true)->first();

        return view('school-admin.dashboard', compact('stats', 'activeYear'));
    }
}
