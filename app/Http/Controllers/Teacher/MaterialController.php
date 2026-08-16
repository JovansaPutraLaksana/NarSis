<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\TeachingAssignment;
use Illuminate\Http\Request;
use App\Services\FileStorage;
use Illuminate\Validation\Rule;

class MaterialController extends Controller
{
    public function index()
    {
        $profileId = auth()->user()->teacherProfile->id;
        $materials = Material::with(['teachingAssignment.subject', 'teachingAssignment.schoolClass', 'teachingAssignment.semester'])
            ->where('teacher_profile_id', $profileId)->latest('published_at')->paginate(15);
        return view('teacher.materials.index', compact('materials'));
    }

    public function create()
    {
        return view('teacher.materials.create', ['assignments' => $this->assignments()]);
    }

    public function store(Request $request)
    {
        $profile = auth()->user()->teacherProfile;
        $schoolId = auth()->user()->school_id;
        $validated = $request->validate([
            'teaching_assignment_id' => ['required', Rule::exists('teaching_assignments', 'id')->where('school_id', $schoolId)->where('teacher_profile_id', $profile->id)->where('is_active', true)],
            'title' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:'.config('narsis.upload_max_kb', 4096), 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,jpg,jpeg,png'],
        ]);

        $assignment = TeachingAssignment::with('semester')->findOrFail($validated['teaching_assignment_id']);
        abort_unless($assignment->is_active && $assignment->semester?->is_active, 422, 'Kelas/mapel tidak berada pada semester aktif.');

        $path = null; $name = null;
        if ($request->hasFile('file')) {
            $path = app(FileStorage::class)->store($request->file('file'), "schools/{$schoolId}/materials");
            $name = $request->file('file')->getClientOriginalName();
        }

        Material::create([
            'teaching_assignment_id' => $validated['teaching_assignment_id'], 'teacher_profile_id' => $profile->id,
            'title' => $validated['title'], 'description' => $validated['description'] ?? null,
            'file_path' => $path, 'file_name' => $name, 'published_at' => now(),
        ]);
        return redirect()->route('teacher.materials.index')->with('success', 'Materi berhasil dipublikasikan.');
    }

    public function edit(Material $material)
    {
        abort_unless($material->teacher_profile_id === auth()->user()->teacherProfile->id, 404);

        return view('teacher.materials.edit', [
            'material' => $material,
            'assignments' => $this->assignments(),
        ]);
    }

    public function update(Request $request, Material $material)
    {
        abort_unless($material->teacher_profile_id === auth()->user()->teacherProfile->id, 404);

        $profile = auth()->user()->teacherProfile;
        $schoolId = auth()->user()->school_id;
        $validated = $request->validate([
            'teaching_assignment_id' => ['required', Rule::exists('teaching_assignments', 'id')->where('school_id', $schoolId)->where('teacher_profile_id', $profile->id)->where('is_active', true)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:'.config('narsis.upload_max_kb', 4096), 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,jpg,jpeg,png'],
            'remove_file' => ['nullable', 'boolean'],
        ]);

        $assignment = TeachingAssignment::with('semester')->findOrFail($validated['teaching_assignment_id']);
        abort_unless($assignment->is_active && $assignment->semester?->is_active, 422, 'Kelas/mapel tidak berada pada semester aktif.');

        $path = $material->file_path;
        $name = $material->file_name;
        $files = app(FileStorage::class);

        if ($request->hasFile('file')) {
            $newPath = $files->store($request->file('file'), "schools/{$schoolId}/materials");
            $files->delete($path);
            $path = $newPath;
            $name = $request->file('file')->getClientOriginalName();
        } elseif ($request->boolean('remove_file')) {
            $files->delete($path);
            $path = null;
            $name = null;
        }

        $material->update([
            'teaching_assignment_id' => $validated['teaching_assignment_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_path' => $path,
            'file_name' => $name,
        ]);

        return redirect()->route('teacher.materials.index')->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy(Material $material)
    {
        abort_unless($material->teacher_profile_id === auth()->user()->teacherProfile->id, 404);
        app(FileStorage::class)->delete($material->file_path);
        $material->delete();
        return back()->with('success', 'Materi berhasil dihapus.');
    }

    private function assignments()
    {
        return TeachingAssignment::with(['subject', 'schoolClass', 'semester.academicYear'])
            ->where('teacher_profile_id', auth()->user()->teacherProfile->id)->where('is_active', true)
            ->whereHas('semester', fn ($q) => $q->where('is_active', true))->get();
    }
}
