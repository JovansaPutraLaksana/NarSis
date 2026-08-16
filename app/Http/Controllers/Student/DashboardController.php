<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\ClassEnrollment;
use App\Models\Schedule;
use App\Models\Semester;

class DashboardController extends Controller
{
    public function index()
    {
        $student = auth()->user()->studentProfile;
        $activeSemester = Semester::with('academicYear')->where('is_active', true)->first();
        $enrollment = null;
        $todaySchedules = collect();
        $upcomingAssignments = collect();

        if ($student && $activeSemester) {
            $enrollment = ClassEnrollment::with('schoolClass')
                ->where('student_profile_id', $student->id)
                ->where('academic_year_id', $activeSemester->academic_year_id)
                ->where('status', 'active')->first();

            if ($enrollment) {
                $todaySchedules = Schedule::with(['teachingAssignment.subject', 'teachingAssignment.teacher.user'])
                    ->where('semester_id', $activeSemester->id)->where('day_of_week', now()->dayOfWeekIso)->where('is_active', true)
                    ->whereHas('teachingAssignment', fn ($q) => $q->where('school_class_id', $enrollment->school_class_id)->where('is_active', true))
                    ->orderBy('start_time')->get();

                $upcomingAssignments = Assignment::with('teachingAssignment.subject')
                    ->where('published_at', '<=', now())->where('due_at', '>=', now())
                    ->whereHas('teachingAssignment', fn ($q) => $q->where('semester_id', $activeSemester->id)->where('school_class_id', $enrollment->school_class_id)->where('is_active', true))
                    ->orderBy('due_at')->limit(5)->get();
            }
        }

        return view('student.dashboard', compact('student', 'activeSemester', 'enrollment', 'todaySchedules', 'upcomingAssignments'));
    }
}
