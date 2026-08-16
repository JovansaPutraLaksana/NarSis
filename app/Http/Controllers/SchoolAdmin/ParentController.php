<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\ParentProfile;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ParentController extends Controller
{
    public function index(Request $request)
    {
        $parents = ParentProfile::with(['user', 'students.user'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q')->trim().'%';
                $query->whereHas('user', fn ($q) => $q->where('name', 'like', $term)->orWhere('email', 'like', $term));
            })->latest()->paginate(15)->withQueryString();
        return view('school-admin.parents.index', compact('parents'));
    }

    public function create()
    {
        $students = StudentProfile::with('user')->whereHas('user', fn ($q) => $q->where('is_active', true))->get()->sortBy('user.name');
        return view('school-admin.parents.create', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateParent($request);
        $schoolId = auth()->user()->school_id;
        $studentIds = collect($validated['student_ids'] ?? [])->map(fn ($id) => (int) $id);
        abort_if(StudentProfile::whereIn('id', $studentIds)->count() !== $studentIds->count(), 422, 'Terdapat siswa yang tidak valid.');

        DB::transaction(function () use ($validated, $schoolId, $studentIds) {
            $user = User::create([
                'school_id' => $schoolId, 'name' => $validated['name'], 'email' => $validated['email'],
                'password' => $validated['password'], 'role' => UserRole::Parent, 'is_active' => true,
            ]);
            $parent = ParentProfile::create(['user_id' => $user->id, 'phone' => $validated['phone'] ?? null, 'address' => $validated['address'] ?? null]);
            $sync = $studentIds->mapWithKeys(fn ($id) => [$id => ['school_id' => $schoolId, 'relationship' => $validated['relationship']]])->all();
            $parent->students()->sync($sync);
        });
        return redirect()->route('school-admin.parents.index')->with('success', 'Akun orang tua/wali berhasil dibuat.');
    }

    public function edit(ParentProfile $parent)
    {
        $parent->load(['user', 'students']);
        $students = StudentProfile::with('user')->whereHas('user', fn ($q) => $q->where('is_active', true))->get()->sortBy('user.name');
        return view('school-admin.parents.edit', compact('parent', 'students'));
    }

    public function update(Request $request, ParentProfile $parent)
    {
        $parent->load('user');
        $validated = $this->validateParent($request, $parent);
        $schoolId = auth()->user()->school_id;
        $studentIds = collect($validated['student_ids'] ?? [])->map(fn ($id) => (int) $id);
        abort_if(StudentProfile::whereIn('id', $studentIds)->count() !== $studentIds->count(), 422, 'Terdapat siswa yang tidak valid.');

        DB::transaction(function () use ($validated, $parent, $schoolId, $studentIds) {
            $userData = ['name' => $validated['name'], 'email' => $validated['email']];
            if (! empty($validated['password'])) $userData['password'] = $validated['password'];
            $parent->user->update($userData);
            $parent->update(['phone' => $validated['phone'] ?? null, 'address' => $validated['address'] ?? null]);
            $sync = $studentIds->mapWithKeys(fn ($id) => [$id => ['school_id' => $schoolId, 'relationship' => $validated['relationship']]])->all();
            $parent->students()->sync($sync);
        });
        return redirect()->route('school-admin.parents.index')->with('success', 'Data orang tua/wali berhasil diperbarui.');
    }

    public function toggleStatus(ParentProfile $parent)
    {
        $parent->user->update(['is_active' => ! $parent->user->is_active]);
        return back()->with('success', 'Status akun orang tua/wali berhasil diperbarui.');
    }

    private function validateParent(Request $request, ?ParentProfile $parent = null): array
    {
        $request->merge(['email' => strtolower(trim((string) $request->input('email')))]);
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($parent?->user_id)],
            'password' => [$parent ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:30'], 'address' => ['nullable', 'string'],
            'relationship' => ['required', Rule::in(['Ayah', 'Ibu', 'Wali'])],
            'student_ids' => ['nullable', 'array'], 'student_ids.*' => ['integer'],
        ]);
    }
}
