<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $subjects = Subject::when($request->filled('q'), function ($q) use ($request) {
            $term = '%'.$request->string('q')->trim().'%';
            $q->where(fn ($x) => $x->where('name', 'like', $term)->orWhere('code', 'like', $term));
        })->orderBy('name')->paginate(15)->withQueryString();
        return view('school-admin.subjects.index', compact('subjects'));
    }
    public function create() { return view('school-admin.subjects.create'); }
    public function store(Request $request)
    {
        $request->merge(['code' => strtoupper(trim((string) $request->input('code')))]);
        $schoolId = auth()->user()->school_id;
        $data = $request->validate([
            'code' => ['required', 'string', 'max:30', Rule::unique('subjects', 'code')->where(fn ($q) => $q->where('school_id', $schoolId))],
            'name' => ['required', 'string', 'max:150'], 'description' => ['nullable', 'string'],
        ]);
        Subject::create([...$data, 'code' => strtoupper($data['code']), 'is_active' => true]);
        return redirect()->route('school-admin.subjects.index')->with('success', 'Mata pelajaran berhasil dibuat.');
    }
    public function edit(Subject $subject) { return view('school-admin.subjects.edit', compact('subject')); }
    public function update(Request $request, Subject $subject)
    {
        $request->merge(['code' => strtoupper(trim((string) $request->input('code')))]);
        $schoolId = auth()->user()->school_id;
        $data = $request->validate([
            'code' => ['required', 'string', 'max:30', Rule::unique('subjects', 'code')->where(fn ($q) => $q->where('school_id', $schoolId))->ignore($subject->id)],
            'name' => ['required', 'string', 'max:150'], 'description' => ['nullable', 'string'],
        ]);
        $subject->update([...$data, 'code' => strtoupper($data['code'])]);
        return redirect()->route('school-admin.subjects.index')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }
    public function toggleStatus(Subject $subject) { $subject->update(['is_active' => ! $subject->is_active]); return back()->with('success', 'Status mata pelajaran berhasil diperbarui.'); }
}
