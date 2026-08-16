<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ClassEnrollment;
use App\Models\Semester;
use Illuminate\Http\Request;
use App\Services\FileStorage;

class AssignmentController extends Controller
{
    public function index()
    {
        $student = auth()->user()->studentProfile;
        $semester = Semester::where('is_active', true)->first();
        $assignments = collect();
        if ($semester) {
            $classId = ClassEnrollment::where('student_profile_id', $student->id)->where('academic_year_id', $semester->academic_year_id)->where('status', 'active')->value('school_class_id');
            if ($classId) {
                $assignments = Assignment::with(['teachingAssignment.subject', 'submissions' => fn ($q) => $q->where('student_profile_id', $student->id)])
                    ->where('published_at', '<=', now())
                    ->whereHas('teachingAssignment', fn ($q) => $q->where('semester_id', $semester->id)->where('school_class_id', $classId)->where('is_active', true))
                    ->orderBy('due_at')->paginate(20);
            }
        }
        return view('student.assignments.index', compact('assignments', 'semester'));
    }

    public function show(Assignment $assignment)
    {
        $this->assertAccessible($assignment);
        $assignment->load(['teachingAssignment.subject', 'teachingAssignment.teacher.user']);
        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)->where('student_profile_id', auth()->user()->studentProfile->id)->first();
        return view('student.assignments.show', compact('assignment', 'submission'));
    }

    public function submit(Request $request, Assignment $assignment)
    {
        $this->assertAccessible($assignment);
        abort_if(now()->greaterThan($assignment->due_at), 422, 'Tenggat pengumpulan tugas telah berakhir.');
        $validated = $request->validate([
            'file' => ['nullable', 'file', 'max:'.config('narsis.upload_max_kb', 4096), 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,jpg,jpeg,png'],
            'note' => ['nullable', 'string', 'max:3000'],
        ]);
        if (! $request->hasFile('file') && blank($validated['note'] ?? null)) {
            return back()->withErrors(['file' => 'Unggah file atau isi catatan jawaban.']);
        }
        $student = auth()->user()->studentProfile; $schoolId = auth()->user()->school_id;
        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)->where('student_profile_id', $student->id)->first();
        $path = $submission?->file_path; $name = $submission?->file_name;
        if ($request->hasFile('file')) {
            if ($path) app(FileStorage::class)->delete($path);
            $path = app(FileStorage::class)->store($request->file('file'), "schools/{$schoolId}/submissions");
            $name = $request->file('file')->getClientOriginalName();
        }
        AssignmentSubmission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'student_profile_id' => $student->id],
            ['file_path' => $path, 'file_name' => $name, 'note' => $validated['note'] ?? null, 'submitted_at' => now(), 'score' => null, 'feedback' => null, 'graded_by' => null, 'graded_at' => null]
        );
        return back()->with('success', 'Tugas berhasil dikumpulkan.');
    }

    private function assertAccessible(Assignment $assignment): void
    {
        $semester = Semester::where('is_active', true)->firstOrFail();
        $classId = ClassEnrollment::where('student_profile_id', auth()->user()->studentProfile->id)->where('academic_year_id', $semester->academic_year_id)->where('status', 'active')->value('school_class_id');
        abort_unless($classId && $assignment->published_at && $assignment->published_at <= now() && $assignment->teachingAssignment()->where('semester_id', $semester->id)->where('school_class_id', $classId)->where('is_active', true)->exists(), 404);
    }
}
