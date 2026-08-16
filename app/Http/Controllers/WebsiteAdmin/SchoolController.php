<?php

namespace App\Http\Controllers\WebsiteAdmin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SchoolController extends Controller
{
    public function index(Request $request)
    {
        $schools = School::query()
            ->withCount('users')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q')->trim().'%';
                $query->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('code', 'like', $term));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('website-admin.schools.index', compact('schools'));
    }

    public function create()
    {
        return view('website-admin.schools.create');
    }

    public function store(Request $request)
    {
        $request->merge([
            'code' => strtoupper(trim((string) $request->input('code'))),
            'admin_email' => strtolower(trim((string) $request->input('admin_email'))),
        ]);
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'alpha_dash', 'unique:schools,code'],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        DB::transaction(function () use ($validated) {
            $school = School::create([
                'code' => strtoupper($validated['code']),
                'name' => $validated['name'],
                'address' => $validated['address'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'is_active' => true,
            ]);

            User::create([
                'school_id' => $school->id,
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => $validated['admin_password'],
                'role' => UserRole::SchoolAdmin,
                'is_active' => true,
            ]);
        });

        return redirect()->route('website-admin.schools.index')
            ->with('success', 'Sekolah dan Admin Sekolah berhasil dibuat.');
    }

    public function show(School $school)
    {
        $school->load(['users' => fn ($query) => $query->orderBy('role')->orderBy('name')]);

        return view('website-admin.schools.show', compact('school'));
    }

    public function edit(School $school)
    {
        return view('website-admin.schools.edit', compact('school'));
    }

    public function update(Request $request, School $school)
    {
        $request->merge(['code' => strtoupper(trim((string) $request->input('code')))]);
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'alpha_dash', Rule::unique('schools', 'code')->ignore($school->id)],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $school->update([...$validated, 'code' => strtoupper($validated['code'])]);

        return redirect()->route('website-admin.schools.show', $school)
            ->with('success', 'Data sekolah berhasil diperbarui.');
    }

    public function toggleStatus(School $school)
    {
        $school->update(['is_active' => ! $school->is_active]);

        return back()->with('success', $school->is_active ? 'Sekolah berhasil diaktifkan.' : 'Sekolah berhasil dinonaktifkan.');
    }
}
