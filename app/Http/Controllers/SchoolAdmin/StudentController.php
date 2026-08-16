<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = StudentProfile::with(['user', 'enrollments.schoolClass'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q')->trim().'%';
                $query->where(function ($filter) use ($term) {
                    $filter->whereHas('user', fn ($q) => $q->where('name', 'like', $term)->orWhere('email', 'like', $term))
                        ->orWhere('nis', 'like', $term)
                        ->orWhere('nisn', 'like', $term);
                });
            })
            ->latest()->paginate(15)->withQueryString();
        return view('school-admin.students.index', compact('students'));
    }

    public function create() { return view('school-admin.students.create'); }

    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $validated = $this->validateStudent($request, $schoolId);

        DB::transaction(function () use ($validated, $schoolId) {
            $user = User::create([
                'school_id' => $schoolId, 'name' => $validated['name'], 'email' => $validated['email'],
                'password' => $validated['password'], 'role' => UserRole::Student, 'is_active' => true,
            ]);
            StudentProfile::create([
                'user_id' => $user->id, 'nis' => $validated['nis'] ?? null, 'nisn' => $validated['nisn'] ?? null,
                'gender' => $validated['gender'] ?? null, 'birth_place' => $validated['birth_place'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null, 'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);
        });
        return redirect()->route('school-admin.students.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function edit(StudentProfile $student) { $student->load('user'); return view('school-admin.students.edit', compact('student')); }

    public function update(Request $request, StudentProfile $student)
    {
        $student->load('user');
        $validated = $this->validateStudent($request, auth()->user()->school_id, $student);
        DB::transaction(function () use ($validated, $student) {
            $userData = ['name' => $validated['name'], 'email' => $validated['email']];
            if (! empty($validated['password'])) $userData['password'] = $validated['password'];
            $student->user->update($userData);
            $student->update([
                'nis' => $validated['nis'] ?? null, 'nisn' => $validated['nisn'] ?? null,
                'gender' => $validated['gender'] ?? null, 'birth_place' => $validated['birth_place'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null, 'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);
        });
        return redirect()->route('school-admin.students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function toggleStatus(StudentProfile $student)
    {
        $student->user->update(['is_active' => ! $student->user->is_active]);
        return back()->with('success', 'Status siswa berhasil diperbarui.');
    }

    private function validateStudent(Request $request, int $schoolId, ?StudentProfile $student = null): array
    {
        $request->merge(['email' => strtolower(trim((string) $request->input('email')))]);
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($student?->user_id)],
            'password' => [$student ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'nis' => ['nullable', 'string', 'max:50', Rule::unique('student_profiles', 'nis')->where(fn ($q) => $q->where('school_id', $schoolId))->ignore($student?->id)],
            'nisn' => ['nullable', 'string', 'max:50', Rule::unique('student_profiles', 'nisn')->where(fn ($q) => $q->where('school_id', $schoolId))->ignore($student?->id)],
            'gender' => ['nullable', Rule::in(['L', 'P'])],
            'birth_place' => ['nullable', 'string', 'max:100'], 'birth_date' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:30'], 'address' => ['nullable', 'string'],
        ]);
    }
}
