<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ClassEnrollment;
use App\Models\TeachingAssignment;
use Illuminate\Http\Request;
use App\Services\FileStorage;
use Illuminate\Validation\Rule;

class AssignmentController extends Controller
{
    public function index()
    {
        $profileId = auth()->user()->teacherProfile->id;
        $assignments = Assignment::with(['teachingAssignment.subject', 'teachingAssignment.schoolClass'])->withCount('submissions')
            ->where('teacher_profile_id', $profileId)->latest()->paginate(15);
        return view('teacher.assignments.index', compact('assignments'));
    }

    public function create() { return view('teacher.assignments.create', ['assignments' => $this->teachingAssignments()]); }

    public function store(Request $request)
    {
        $profile = auth()->user()->teacherProfile; $schoolId = auth()->user()->school_id;
        $validated = $request->validate([
            'teaching_assignment_id' => ['required', Rule::exists('teaching_assignments', 'id')->where('school_id', $schoolId)->where('teacher_profile_id', $profile->id)->where('is_active', true)],
            'title' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string'],
            'due_at' => ['required', 'date', 'after:now'], 'max_score' => ['required', 'numeric', 'min:1', 'max:9999.99'],
            'file' => ['nullable', 'file', 'max:'.config('narsis.upload_max_kb', 4096), 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,jpg,jpeg,png'],
        ]);
        $teachingAssignment = TeachingAssignment::with('semester')->findOrFail($validated['teaching_assignment_id']);
        abort_unless($teachingAssignment->is_active && $teachingAssignment->semester?->is_active, 422, 'Kelas/mapel tidak berada pada semester aktif.');

        $path = null; $name = null;
        if ($request->hasFile('file')) { $path = app(FileStorage::class)->store($request->file('file'), "schools/{$schoolId}/assignments"); $name = $request->file('file')->getClientOriginalName(); }
        Assignment::create([
            'teaching_assignment_id' => $validated['teaching_assignment_id'], 'teacher_profile_id' => $profile->id,
            'title' => $validated['title'], 'description' => $validated['description'] ?? null,
            'file_path' => $path, 'file_name' => $name, 'due_at' => $validated['due_at'], 'max_score' => $validated['max_score'], 'published_at' => now(),
        ]);
        return redirect()->route('teacher.assignments.index')->with('success', 'Tugas berhasil dipublikasikan.');
    }

    public function edit(Assignment $assignment)
    {
        $this->assertOwned($assignment);
        $assignment->load(['teachingAssignment.subject', 'teachingAssignment.schoolClass']);

        return view('teacher.assignments.edit', compact('assignment'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $this->assertOwned($assignment);
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_at' => ['required', 'date'],
            'max_score' => ['required', 'numeric', 'min:1', 'max:9999.99'],
            'file' => ['nullable', 'file', 'max:'.config('narsis.upload_max_kb', 4096), 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,jpg,jpeg,png'],
            'remove_file' => ['nullable', 'boolean'],
        ]);

        $highestScore = $assignment->submissions()->whereNotNull('score')->max('score');
        if ($highestScore !== null && (float) $validated['max_score'] < (float) $highestScore) {
            return back()->withErrors([
                'max_score' => 'Nilai maksimal tidak boleh lebih rendah dari nilai yang sudah diberikan ('.$highestScore.').',
            ])->withInput();
        }

        $path = $assignment->file_path;
        $name = $assignment->file_name;
        $files = app(FileStorage::class);
        $schoolId = auth()->user()->school_id;

        if ($request->hasFile('file')) {
            $newPath = $files->store($request->file('file'), "schools/{$schoolId}/assignments");
            $files->delete($path);
            $path = $newPath;
            $name = $request->file('file')->getClientOriginalName();
        } elseif ($request->boolean('remove_file')) {
            $files->delete($path);
            $path = null;
            $name = null;
        }

        $assignment->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'due_at' => $validated['due_at'],
            'max_score' => $validated['max_score'],
            'file_path' => $path,
            'file_name' => $name,
        ]);

        return redirect()->route('teacher.assignments.index')->with('success', 'Tugas berhasil diperbarui.');
    }

    public function submissions(Assignment $assignment)
    {
        $this->assertOwned($assignment);
        $assignment->load(['teachingAssignment.subject', 'teachingAssignment.schoolClass', 'teachingAssignment.semester', 'submissions.student.user']);
        $students = ClassEnrollment::with('student.user')
            ->where('school_class_id', $assignment->teachingAssignment->school_class_id)
            ->where('academic_year_id', $assignment->teachingAssignment->semester->academic_year_id)
            ->where('status', 'active')
            ->get()
            ->sortBy('student.user.name');
        $submissions = $assignment->submissions->keyBy('student_profile_id');
        return view('teacher.assignments.submissions', compact('assignment', 'students', 'submissions'));
    }

    public function grade(Request $request, Assignment $assignment, AssignmentSubmission $submission)
    {
        $this->assertOwned($assignment);
        abort_unless($submission->assignment_id === $assignment->id, 404);
        $validated = $request->validate([
            'score' => ['required', 'numeric', 'min:0', 'max:'.$assignment->max_score], 'feedback' => ['nullable', 'string', 'max:2000'],
        ]);
        $submission->update(['score' => $validated['score'], 'feedback' => $validated['feedback'] ?? null, 'graded_by' => auth()->id(), 'graded_at' => now()]);
        return back()->with('success', 'Nilai berhasil disimpan.');
    }

    public function destroy(Assignment $assignment)
    {
        $this->assertOwned($assignment);
        abort_if($assignment->submissions()->exists(), 422, 'Tugas yang sudah memiliki pengumpulan tidak dapat dihapus.');
        app(FileStorage::class)->delete($assignment->file_path);
        $assignment->delete();
        return back()->with('success', 'Tugas berhasil dihapus.');
    }

    private function teachingAssignments()
    {
        return TeachingAssignment::with(['subject', 'schoolClass', 'semester.academicYear'])
            ->where('teacher_profile_id', auth()->user()->teacherProfile->id)->where('is_active', true)
            ->whereHas('semester', fn ($q) => $q->where('is_active', true))->get();
    }

    private function assertOwned(Assignment $assignment): void
    {
        abort_unless($assignment->teacher_profile_id === auth()->user()->teacherProfile->id, 404);
    }
}
