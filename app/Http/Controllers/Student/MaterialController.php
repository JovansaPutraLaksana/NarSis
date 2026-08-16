<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClassEnrollment;
use App\Models\Material;
use App\Models\Semester;

class MaterialController extends Controller
{
    public function index()
    {
        $student = auth()->user()->studentProfile;
        $semester = Semester::where('is_active', true)->first();
        $materials = collect();

        if ($semester) {
            $classId = ClassEnrollment::where('student_profile_id', $student->id)->where('academic_year_id', $semester->academic_year_id)->where('status', 'active')->value('school_class_id');
            if ($classId) {
                $materials = Material::with(['teachingAssignment.subject', 'teacher.user'])
                    ->where('published_at', '<=', now())
                    ->whereHas('teachingAssignment', fn ($q) => $q->where('semester_id', $semester->id)->where('school_class_id', $classId)->where('is_active', true))
                    ->latest('published_at')->paginate(20);
            }
        }

        return view('student.materials.index', compact('materials', 'semester'));
    }
}
