<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassEnrollment;
use App\Models\SchoolClass;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SchoolClassController extends Controller
{
    public function index(Request $request)
    {
        $classes = SchoolClass::with(['academicYear', 'homeroomTeacher.user'])->withCount('enrollments')
            ->when($request->filled('year'), fn ($q) => $q->where('academic_year_id', $request->integer('year')))
            ->orderByDesc('academic_year_id')->orderBy('name')->paginate(15)->withQueryString();
        $years = AcademicYear::latest('start_date')->get();
        return view('school-admin.classes.index', compact('classes', 'years'));
    }

    public function create()
    {
        $years = AcademicYear::latest('start_date')->get();
        $teachers = TeacherProfile::with('user')->whereHas('user', fn ($q) => $q->where('is_active', true))->get()->sortBy('user.name');
        return view('school-admin.classes.create', compact('years', 'teachers'));
    }

    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $validated = $request->validate([
            'academic_year_id' => ['required', Rule::exists('academic_years', 'id')->where('school_id', $schoolId)],
            'name' => ['required', 'string', 'max:100', Rule::unique('school_classes', 'name')->where(fn ($q) => $q->where('school_id', $schoolId)->where('academic_year_id', $request->integer('academic_year_id')))],
            'grade_level' => ['nullable', 'string', 'max:20'], 'room' => ['nullable', 'string', 'max:50'],
            'homeroom_teacher_id' => ['nullable', Rule::exists('teacher_profiles', 'id')->where('school_id', $schoolId)],
        ]);
        SchoolClass::create([...$validated, 'is_active' => true]);
        return redirect()->route('school-admin.classes.index')->with('success', 'Kelas berhasil dibuat.');
    }

    public function edit(SchoolClass $class)
    {
        $years = AcademicYear::latest('start_date')->get();
        $teachers = TeacherProfile::with('user')->whereHas('user', fn ($q) => $q->where('is_active', true))->get()->sortBy('user.name');
        return view('school-admin.classes.edit', compact('class', 'years', 'teachers'));
    }

    public function update(Request $request, SchoolClass $class)
    {
        $schoolId = auth()->user()->school_id;
        $validated = $request->validate([
            'academic_year_id' => ['required', Rule::exists('academic_years', 'id')->where('school_id', $schoolId)],
            'name' => ['required', 'string', 'max:100', Rule::unique('school_classes', 'name')->where(fn ($q) => $q->where('school_id', $schoolId)->where('academic_year_id', $request->integer('academic_year_id')))->ignore($class->id)],
            'grade_level' => ['nullable', 'string', 'max:20'], 'room' => ['nullable', 'string', 'max:50'],
            'homeroom_teacher_id' => ['nullable', Rule::exists('teacher_profiles', 'id')->where('school_id', $schoolId)],
        ]);
        abort_if($class->academic_year_id !== (int) $validated['academic_year_id'] && $class->enrollments()->exists(), 422, 'Tahun ajaran kelas tidak dapat diubah karena sudah memiliki siswa.');
        $class->update($validated);
        return redirect()->route('school-admin.classes.index')->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function students(SchoolClass $class)
    {
        $class->load(['academicYear', 'enrollments.student.user']);
        $students = StudentProfile::with('user')->whereHas('user', fn ($q) => $q->where('is_active', true))->get()->sortBy('user.name');
        $selectedIds = $class->enrollments->pluck('student_profile_id')->all();
        return view('school-admin.classes.students', compact('class', 'students', 'selectedIds'));
    }

    public function syncStudents(Request $request, SchoolClass $class)
    {
        $validated = $request->validate(['student_ids' => ['nullable', 'array'], 'student_ids.*' => ['integer']]);
        $studentIds = collect($validated['student_ids'] ?? [])->map(fn ($id) => (int) $id)->unique();
        abort_if(StudentProfile::whereIn('id', $studentIds)->count() !== $studentIds->count(), 422, 'Terdapat siswa yang tidak valid.');
        $schoolId = auth()->user()->school_id;

        DB::transaction(function () use ($studentIds, $class, $schoolId) {
            ClassEnrollment::where('school_class_id', $class->id)->whereNotIn('student_profile_id', $studentIds)->delete();
            foreach ($studentIds as $studentId) {
                ClassEnrollment::updateOrCreate(
                    ['school_id' => $schoolId, 'academic_year_id' => $class->academic_year_id, 'student_profile_id' => $studentId],
                    ['school_class_id' => $class->id, 'enrolled_at' => now()->toDateString(), 'status' => 'active']
                );
            }
        });
        return back()->with('success', 'Daftar siswa kelas berhasil disimpan.');
    }

    public function toggleStatus(SchoolClass $class)
    {
        $class->update(['is_active' => ! $class->is_active]);
        return back()->with('success', 'Status kelas berhasil diperbarui.');
    }
}
