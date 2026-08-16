<?php

namespace App\Http\Controllers\ParentUser;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AttendanceRecord;
use App\Models\ClassEnrollment;
use App\Models\Semester;
use App\Models\StudentProfile;

class ChildController extends Controller
{
    public function show(StudentProfile $student)
    {
        $parent = auth()->user()->parentProfile;
        abort_unless($parent->students()->where('student_profiles.id', $student->id)->exists(), 404);

        $student->load('user');
        $semester = Semester::with('academicYear')->where('is_active', true)->first();
        $enrollment = null;
        $assignments = collect();

        if ($semester) {
            $enrollment = ClassEnrollment::with('schoolClass')
                ->where('student_profile_id', $student->id)
                ->where('academic_year_id', $semester->academic_year_id)
                ->where('status', 'active')->first();

            if ($enrollment) {
                $assignments = Assignment::with([
                    'teachingAssignment.subject',
                    'submissions' => fn ($q) => $q->where('student_profile_id', $student->id),
                ])->where('published_at', '<=', now())
                    ->whereHas('teachingAssignment', fn ($q) => $q->where('semester_id', $semester->id)->where('school_class_id', $enrollment->school_class_id)->where('is_active', true))
                    ->orderBy('due_at')->get();
            }
        }

        $attendance = AttendanceRecord::with(['session.schedule.teachingAssignment.subject'])
            ->where('student_profile_id', $student->id)
            ->latest('id')->limit(30)->get();

        $attendanceSummary = AttendanceRecord::where('student_profile_id', $student->id)
            ->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('parent.children.show', compact('student', 'semester', 'enrollment', 'assignments', 'attendance', 'attendanceSummary'));
    }
}
