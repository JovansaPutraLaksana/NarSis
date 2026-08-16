<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;

class AttendanceController extends Controller
{
    public function index()
    {
        $student = auth()->user()->studentProfile;
        $records = AttendanceRecord::with(['session.schedule.teachingAssignment.subject', 'session.schedule.teachingAssignment.schoolClass'])
            ->where('student_profile_id', $student->id)
            ->whereHas('session')
            ->latest('id')->paginate(30);

        $summary = AttendanceRecord::where('student_profile_id', $student->id)
            ->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('student.attendance.index', compact('records', 'summary'));
    }
}
