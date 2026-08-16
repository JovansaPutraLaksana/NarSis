<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ClassEnrollment;
use App\Models\Material;
use App\Services\FileStorage;

class FileController extends Controller
{
    public function material(Material $material, FileStorage $files)
    {
        abort_unless($material->file_path && $this->canAccessTeachingAssignment($material->teachingAssignment), 404);
        return $files->download($material->file_path, $material->file_name ?: basename($material->file_path));
    }

    public function assignment(Assignment $assignment, FileStorage $files)
    {
        abort_unless($assignment->file_path && $this->canAccessTeachingAssignment($assignment->teachingAssignment), 404);
        return $files->download($assignment->file_path, $assignment->file_name ?: basename($assignment->file_path));
    }

    public function submission(AssignmentSubmission $submission, FileStorage $files)
    {
        abort_unless($submission->file_path, 404);
        $user = auth()->user();
        $allowed = false;

        if ($user->role === UserRole::SchoolAdmin && $user->school_id === $submission->school_id) $allowed = true;
        if ($user->role === UserRole::Teacher && $submission->assignment->teacher_profile_id === $user->teacherProfile?->id) $allowed = true;
        if ($user->role === UserRole::Student && $submission->student_profile_id === $user->studentProfile?->id) $allowed = true;
        if ($user->role === UserRole::Parent && $user->parentProfile?->students()->where('student_profiles.id', $submission->student_profile_id)->exists()) $allowed = true;

        abort_unless($allowed, 404);
        return $files->download($submission->file_path, $submission->file_name ?: basename($submission->file_path));
    }

    private function canAccessTeachingAssignment($teachingAssignment): bool
    {
        $user = auth()->user();
        if (! $teachingAssignment || $user->school_id !== $teachingAssignment->school_id) return false;
        if ($user->role === UserRole::SchoolAdmin) return true;
        if ($user->role === UserRole::Teacher) return $teachingAssignment->teacher_profile_id === $user->teacherProfile?->id;
        if ($user->role === UserRole::Student) {
            return ClassEnrollment::where('student_profile_id', $user->studentProfile?->id)->where('school_class_id', $teachingAssignment->school_class_id)->exists();
        }
        if ($user->role === UserRole::Parent) {
            $studentIds = $user->parentProfile?->students()->pluck('student_profiles.id') ?? collect();
            return ClassEnrollment::whereIn('student_profile_id', $studentIds)->where('school_class_id', $teachingAssignment->school_class_id)->exists();
        }
        return false;
    }
}
