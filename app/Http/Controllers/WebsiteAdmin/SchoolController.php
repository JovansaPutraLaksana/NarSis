<?php

namespace App\Http\Controllers\WebsiteAdmin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = School::query()
            ->withCount('users')
            ->latest()
            ->paginate(10);

        return view('website-admin.schools.index', compact('schools'));
    }


    public function create()
    {
        return view('website-admin.schools.create');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:20',
                'unique:schools,code',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'admin_name' => [
                'required',
                'string',
                'max:255',
            ],

            'admin_email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'admin_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
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
                'password' => Hash::make($validated['admin_password']),
                'role' => UserRole::SchoolAdmin,
            ]);
        });


        return redirect()
            ->route('website-admin.schools.index')
            ->with('success', 'Sekolah dan Admin Sekolah berhasil dibuat.');
    }


    public function show(School $school)
    {
        $school->load([
            'users' => function ($query) {
                $query->orderBy('name');
            },
        ]);

        return view(
            'website-admin.schools.show',
            compact('school')
        );
    }
}