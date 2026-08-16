<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\ClassEnrollment;
use App\Models\Schedule;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    public function index()
    {
        $profile = auth()->user()->teacherProfile;
        $activeSemester = Semester::where('is_active', true)->first();
        $schedules = collect();

        if ($profile && $activeSemester) {
            $schedules = Schedule::with(['teachingAssignment.schoolClass', 'teachingAssignment.subject'])
                ->where('semester_id', $activeSemester->id)
                ->where('day_of_week', now()->dayOfWeekIso)
                ->where('is_active', true)
                ->whereHas('teachingAssignment', fn ($q) => $q->where('teacher_profile_id', $profile->id)->where('is_active', true))
                ->orderBy('start_time')->get();
        }

        return view('teacher.attendance.index', compact('schedules', 'activeSemester'));
    }

    public function show(Schedule $schedule)
    {
        $this->assertOwnedSchedule($schedule);
        abort_unless($this->isWithinLessonTime($schedule), 403, 'Absensi hanya dapat dibuka saat jam pelajaran sedang berlangsung.');

        $assignment = $schedule->teachingAssignment()->with(['schoolClass', 'subject'])->firstOrFail();
        $students = ClassEnrollment::with('student.user')
            ->where('school_class_id', $assignment->school_class_id)
            ->where('academic_year_id', $assignment->semester->academic_year_id)
            ->where('status', 'active')
            ->get()->sortBy('student.user.name');

        $session = AttendanceSession::firstOrCreate(
            ['schedule_id' => $schedule->id, 'attendance_date' => now()->toDateString()],
            ['opened_by' => auth()->id(), 'opened_at' => now()]
        );
        $records = $session->records()->get()->keyBy('student_profile_id');

        return view('teacher.attendance.show', compact('schedule', 'assignment', 'students', 'session', 'records'));
    }

    public function store(Request $request, Schedule $schedule)
    {
        $this->assertOwnedSchedule($schedule);
        abort_unless($this->isWithinLessonTime($schedule), 403, 'Absensi hanya dapat disimpan saat jam pelajaran sedang berlangsung.');

        $assignment = $schedule->teachingAssignment()->with('semester')->firstOrFail();
        $studentIds = ClassEnrollment::where('school_class_id', $assignment->school_class_id)
            ->where('academic_year_id', $assignment->semester->academic_year_id)
            ->where('status', 'active')->pluck('student_profile_id');

        $validated = $request->validate([
            'attendance' => ['required', 'array'],
            'attendance.*.status' => ['required', Rule::in(array_keys(AttendanceRecord::STATUSES))],
            'attendance.*.note' => ['nullable', 'string', 'max:255'],
        ]);

        $submittedIds = collect(array_keys($validated['attendance']))->map(fn ($id) => (int) $id)->sort()->values();
        $expectedIds = $studentIds->map(fn ($id) => (int) $id)->sort()->values();
        abort_unless(
            $submittedIds->count() === $expectedIds->count() && $submittedIds->diff($expectedIds)->isEmpty(),
            422,
            'Absensi harus diisi untuk seluruh siswa aktif pada kelas ini.'
        );

        DB::transaction(function () use ($schedule, $validated) {
            $session = AttendanceSession::firstOrCreate(
                ['schedule_id' => $schedule->id, 'attendance_date' => now()->toDateString()],
                ['opened_by' => auth()->id(), 'opened_at' => now()]
            );
            foreach ($validated['attendance'] as $studentId => $row) {
                AttendanceRecord::updateOrCreate(
                    ['attendance_session_id' => $session->id, 'student_profile_id' => (int) $studentId],
                    ['status' => $row['status'], 'note' => $row['note'] ?? null]
                );
            }
            $session->update(['submitted_at' => now()]);
        });

        return redirect()->route('teacher.attendance.index')->with('success', 'Absensi berhasil disimpan.');
    }

    private function assertOwnedSchedule(Schedule $schedule): void
    {
        $schedule->load('teachingAssignment.semester');
        abort_unless(
            $schedule->is_active &&
            $schedule->teachingAssignment->is_active &&
            $schedule->teachingAssignment->teacher_profile_id === auth()->user()->teacherProfile?->id &&
            $schedule->semester?->is_active,
            404
        );
    }

    private function isWithinLessonTime(Schedule $schedule): bool
    {
        if ((int) $schedule->day_of_week !== now()->dayOfWeekIso) return false;
        $time = now()->format('H:i:s');
        return $time >= $schedule->start_time && $time <= $schedule->end_time;
    }
}
