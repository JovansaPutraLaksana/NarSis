<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $teachers = TeacherProfile::with('user')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q')->trim().'%';
                $query->where(function ($filter) use ($term) {
                    $filter->whereHas('user', fn ($q) => $q->where('name', 'like', $term)->orWhere('email', 'like', $term))
                        ->orWhere('employee_number', 'like', $term)
                        ->orWhere('nip', 'like', $term);
                });
            })
            ->latest()->paginate(15)->withQueryString();

        return view('school-admin.teachers.index', compact('teachers'));
    }

    public function create() { return view('school-admin.teachers.create'); }

    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $validated = $this->validateTeacher($request, $schoolId);

        DB::transaction(function () use ($validated, $schoolId) {
            $user = User::create([
                'school_id' => $schoolId, 'name' => $validated['name'], 'email' => $validated['email'],
                'password' => $validated['password'], 'role' => UserRole::Teacher, 'is_active' => true,
            ]);
            TeacherProfile::create([
                'user_id' => $user->id, 'employee_number' => $validated['employee_number'] ?? null,
                'nip' => $validated['nip'] ?? null, 'gender' => $validated['gender'] ?? null,
                'phone' => $validated['phone'] ?? null, 'address' => $validated['address'] ?? null,
            ]);
        });

        return redirect()->route('school-admin.teachers.index')->with('success', 'Guru berhasil ditambahkan.');
    }

    public function edit(TeacherProfile $teacher)
    {
        $teacher->load('user');
        return view('school-admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, TeacherProfile $teacher)
    {
        $teacher->load('user');
        $schoolId = auth()->user()->school_id;
        $validated = $this->validateTeacher($request, $schoolId, $teacher);

        DB::transaction(function () use ($validated, $teacher) {
            $userData = ['name' => $validated['name'], 'email' => $validated['email']];
            if (! empty($validated['password'])) $userData['password'] = $validated['password'];
            $teacher->user->update($userData);
            $teacher->update([
                'employee_number' => $validated['employee_number'] ?? null, 'nip' => $validated['nip'] ?? null,
                'gender' => $validated['gender'] ?? null, 'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);
        });

        return redirect()->route('school-admin.teachers.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    public function toggleStatus(TeacherProfile $teacher)
    {
        $teacher->user->update(['is_active' => ! $teacher->user->is_active]);
        return back()->with('success', 'Status guru berhasil diperbarui.');
    }

    private function validateTeacher(Request $request, int $schoolId, ?TeacherProfile $teacher = null): array
    {
        $request->merge(['email' => strtolower(trim((string) $request->input('email')))]);
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($teacher?->user_id)],
            'password' => [$teacher ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'employee_number' => ['nullable', 'string', 'max:50', Rule::unique('teacher_profiles', 'employee_number')->where(fn ($q) => $q->where('school_id', $schoolId))->ignore($teacher?->id)],
            'nip' => ['nullable', 'string', 'max:50', Rule::unique('teacher_profiles', 'nip')->where(fn ($q) => $q->where('school_id', $schoolId))->ignore($teacher?->id)],
            'gender' => ['nullable', Rule::in(['L', 'P'])],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
        ]);
    }
}
