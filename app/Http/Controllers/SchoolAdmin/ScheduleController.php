<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Semester;
use App\Models\TeachingAssignment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $schedules = Schedule::with(['semester.academicYear', 'teachingAssignment.schoolClass', 'teachingAssignment.subject', 'teachingAssignment.teacher.user'])
            ->when($request->filled('semester'), fn ($q) => $q->where('semester_id', $request->integer('semester')))
            ->orderBy('day_of_week')->orderBy('start_time')->paginate(30)->withQueryString();
        $semesters = Semester::with('academicYear')->orderByDesc('start_date')->get();
        return view('school-admin.schedules.index', compact('schedules', 'semesters'));
    }

    public function create() { return view('school-admin.schedules.create', ['assignments' => $this->assignments()]); }

    public function store(Request $request)
    {
        $data = $this->validateSchedule($request);
        $assignment = TeachingAssignment::findOrFail($data['teaching_assignment_id']);
        $data['semester_id'] = $assignment->semester_id;
        if ($this->hasConflict($assignment, $data)) return back()->withErrors(['start_time' => 'Jadwal bentrok dengan jadwal guru atau kelas pada waktu yang sama.'])->withInput();
        Schedule::create([...$data, 'is_active' => true]);
        return redirect()->route('school-admin.schedules.index')->with('success', 'Jadwal pelajaran berhasil dibuat.');
    }

    public function edit(Schedule $schedule) { return view('school-admin.schedules.edit', ['schedule' => $schedule, 'assignments' => $this->assignments()]); }

    public function update(Request $request, Schedule $schedule)
    {
        $data = $this->validateSchedule($request);
        $assignment = TeachingAssignment::findOrFail($data['teaching_assignment_id']);
        $data['semester_id'] = $assignment->semester_id;
        if ($this->hasConflict($assignment, $data, $schedule->id)) return back()->withErrors(['start_time' => 'Jadwal bentrok dengan jadwal guru atau kelas pada waktu yang sama.'])->withInput();
        $schedule->update($data);
        return redirect()->route('school-admin.schedules.index')->with('success', 'Jadwal pelajaran berhasil diperbarui.');
    }

    public function toggleStatus(Schedule $schedule) { $schedule->update(['is_active' => ! $schedule->is_active]); return back()->with('success', 'Status jadwal berhasil diperbarui.'); }

    private function validateSchedule(Request $request): array
    {
        $schoolId = auth()->user()->school_id;
        return $request->validate([
            'teaching_assignment_id' => ['required', Rule::exists('teaching_assignments', 'id')->where('school_id', $schoolId)],
            'day_of_week' => ['required', 'integer', 'between:1,7'],
            'start_time' => ['required', 'date_format:H:i'], 'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room' => ['nullable', 'string', 'max:50'],
        ]);
    }

    private function assignments()
    {
        return TeachingAssignment::with(['semester.academicYear', 'schoolClass', 'subject', 'teacher.user'])
            ->where('is_active', true)->orderByDesc('semester_id')->get();
    }

    private function hasConflict(TeachingAssignment $assignment, array $data, ?int $ignoreId = null): bool
    {
        return Schedule::where('semester_id', $assignment->semester_id)
            ->where('day_of_week', $data['day_of_week'])->where('is_active', true)
            ->where('start_time', '<', $data['end_time'])->where('end_time', '>', $data['start_time'])
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->whereHas('teachingAssignment', fn ($q) => $q->where(fn ($x) => $x->where('teacher_profile_id', $assignment->teacher_profile_id)->orWhere('school_class_id', $assignment->school_class_id)))
            ->exists();
    }
}
