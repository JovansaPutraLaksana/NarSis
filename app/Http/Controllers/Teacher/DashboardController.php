<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Material;
use App\Models\Schedule;
use App\Models\Semester;

class DashboardController extends Controller
{
    public function index()
    {
        $profile = auth()->user()->teacherProfile;
        $activeSemester = Semester::where('is_active', true)->first();
        $todaySchedules = collect();
        $stats = ['materials' => 0, 'assignments' => 0];

        if ($profile && $activeSemester) {
            $todaySchedules = Schedule::with(['teachingAssignment.schoolClass', 'teachingAssignment.subject'])
                ->where('semester_id', $activeSemester->id)
                ->where('day_of_week', now()->dayOfWeekIso)
                ->where('is_active', true)
                ->whereHas('teachingAssignment', fn ($q) => $q->where('teacher_profile_id', $profile->id)->where('is_active', true))
                ->orderBy('start_time')->get();

            $assignmentIds = $profile->teachingAssignments()->where('semester_id', $activeSemester->id)->pluck('id');
            $stats['materials'] = Material::whereIn('teaching_assignment_id', $assignmentIds)->count();
            $stats['assignments'] = Assignment::whereIn('teaching_assignment_id', $assignmentIds)->count();
        }

        return view('teacher.dashboard', compact('profile', 'activeSemester', 'todaySchedules', 'stats'));
    }
}
