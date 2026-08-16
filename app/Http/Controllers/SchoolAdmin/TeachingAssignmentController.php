<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\TeachingAssignment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeachingAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $assignments = TeachingAssignment::with(['semester.academicYear', 'schoolClass', 'subject', 'teacher.user'])
            ->when($request->filled('semester'), fn ($q) => $q->where('semester_id', $request->integer('semester')))
            ->orderByDesc('semester_id')->paginate(20)->withQueryString();
        $semesters = Semester::with('academicYear')->orderByDesc('start_date')->get();
        return view('school-admin.teaching-assignments.index', compact('assignments', 'semesters'));
    }

    public function create()
    {
        return view('school-admin.teaching-assignments.create', $this->formData());
    }

    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $data = $request->validate([
            'semester_id' => ['required', Rule::exists('semesters', 'id')->where('school_id', $schoolId)],
            'school_class_id' => ['required', Rule::exists('school_classes', 'id')->where('school_id', $schoolId)],
            'subject_id' => ['required', Rule::exists('subjects', 'id')->where('school_id', $schoolId)],
            'teacher_profile_id' => ['required', Rule::exists('teacher_profiles', 'id')->where('school_id', $schoolId)],
        ]);
        $this->assertClassMatchesSemester($data);
        $exists = TeachingAssignment::where('semester_id', $data['semester_id'])->where('school_class_id', $data['school_class_id'])->where('subject_id', $data['subject_id'])->exists();
        if ($exists) return back()->withErrors(['subject_id' => 'Mata pelajaran ini sudah memiliki guru pengampu pada kelas dan semester tersebut.'])->withInput();
        TeachingAssignment::create([...$data, 'is_active' => true]);
        return redirect()->route('school-admin.teaching-assignments.index')->with('success', 'Guru pengampu berhasil ditetapkan.');
    }

    public function edit(TeachingAssignment $teachingAssignment) { return view('school-admin.teaching-assignments.edit', ['teachingAssignment' => $teachingAssignment] + $this->formData()); }

    public function update(Request $request, TeachingAssignment $teachingAssignment)
    {
        $schoolId = auth()->user()->school_id;
        $data = $request->validate([
            'semester_id' => ['required', Rule::exists('semesters', 'id')->where('school_id', $schoolId)],
            'school_class_id' => ['required', Rule::exists('school_classes', 'id')->where('school_id', $schoolId)],
            'subject_id' => ['required', Rule::exists('subjects', 'id')->where('school_id', $schoolId)],
            'teacher_profile_id' => ['required', Rule::exists('teacher_profiles', 'id')->where('school_id', $schoolId)],
        ]);
        $this->assertClassMatchesSemester($data);
        $exists = TeachingAssignment::where('semester_id', $data['semester_id'])->where('school_class_id', $data['school_class_id'])->where('subject_id', $data['subject_id'])->where('id', '!=', $teachingAssignment->id)->exists();
        if ($exists) return back()->withErrors(['subject_id' => 'Kombinasi kelas, semester, dan mata pelajaran sudah digunakan.'])->withInput();
        $teachingAssignment->update($data);
        return redirect()->route('school-admin.teaching-assignments.index')->with('success', 'Guru pengampu berhasil diperbarui.');
    }

    public function toggleStatus(TeachingAssignment $teachingAssignment) { $teachingAssignment->update(['is_active' => ! $teachingAssignment->is_active]); return back()->with('success', 'Status guru pengampu berhasil diperbarui.'); }

    private function formData(): array
    {
        return [
            'semesters' => Semester::with('academicYear')->orderByDesc('start_date')->get(),
            'classes' => SchoolClass::with('academicYear')->where('is_active', true)->orderBy('name')->get(),
            'subjects' => Subject::where('is_active', true)->orderBy('name')->get(),
            'teachers' => TeacherProfile::with('user')->whereHas('user', fn ($q) => $q->where('is_active', true))->get()->sortBy('user.name'),
        ];
    }

    private function assertClassMatchesSemester(array $data): void
    {
        $semester = Semester::findOrFail($data['semester_id']);
        $class = SchoolClass::findOrFail($data['school_class_id']);
        abort_if($class->academic_year_id !== $semester->academic_year_id, 422, 'Kelas dan semester harus berada pada tahun ajaran yang sama.');
    }
}
