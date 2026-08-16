<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::with('semesters')->latest('start_date')->get();

        return view('school-admin.academic-years.index', compact('academicYears'));
    }

    public function create()
    {
        return view('school-admin.academic-years.create');
    }

    public function store(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:20',
                Rule::unique('academic_years', 'name')->where(fn ($query) => $query->where('school_id', $schoolId)),
            ],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'odd_start_date' => ['required', 'date', 'after_or_equal:start_date'],
            'odd_end_date' => ['required', 'date', 'after_or_equal:odd_start_date', 'before_or_equal:end_date'],
            'even_start_date' => ['required', 'date', 'after:odd_end_date', 'before_or_equal:end_date'],
            'even_end_date' => ['required', 'date', 'after_or_equal:even_start_date', 'before_or_equal:end_date'],
        ]);

        DB::transaction(function () use ($validated) {
            $academicYear = AcademicYear::create([
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_active' => false,
            ]);

            Semester::create([
                'academic_year_id' => $academicYear->id,
                'name' => 'Ganjil',
                'start_date' => $validated['odd_start_date'],
                'end_date' => $validated['odd_end_date'],
                'is_active' => false,
            ]);

            Semester::create([
                'academic_year_id' => $academicYear->id,
                'name' => 'Genap',
                'start_date' => $validated['even_start_date'],
                'end_date' => $validated['even_end_date'],
                'is_active' => false,
            ]);
        });

        return redirect()->route('school-admin.academic-years.index')->with('success', 'Tahun ajaran berhasil dibuat.');
    }


    public function edit(AcademicYear $academicYear)
    {
        $academicYear->load('semesters');
        return view('school-admin.academic-years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $schoolId = auth()->user()->school_id;
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:20',
                Rule::unique('academic_years', 'name')->where(fn ($query) => $query->where('school_id', $schoolId))->ignore($academicYear->id),
            ],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'odd_start_date' => ['required', 'date', 'after_or_equal:start_date'],
            'odd_end_date' => ['required', 'date', 'after_or_equal:odd_start_date', 'before_or_equal:end_date'],
            'even_start_date' => ['required', 'date', 'after:odd_end_date', 'before_or_equal:end_date'],
            'even_end_date' => ['required', 'date', 'after_or_equal:even_start_date', 'before_or_equal:end_date'],
        ]);

        DB::transaction(function () use ($validated, $academicYear) {
            $academicYear->update([
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ]);

            Semester::updateOrCreate(
                ['academic_year_id' => $academicYear->id, 'name' => 'Ganjil'],
                ['start_date' => $validated['odd_start_date'], 'end_date' => $validated['odd_end_date'], 'is_active' => $academicYear->semesters()->where('name', 'Ganjil')->value('is_active') ?? false]
            );
            Semester::updateOrCreate(
                ['academic_year_id' => $academicYear->id, 'name' => 'Genap'],
                ['start_date' => $validated['even_start_date'], 'end_date' => $validated['even_end_date'], 'is_active' => $academicYear->semesters()->where('name', 'Genap')->value('is_active') ?? false]
            );
        });

        return redirect()->route('school-admin.academic-years.index')->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function activateSemester(AcademicYear $academicYear, Semester $semester)
    {
        abort_if($semester->academic_year_id !== $academicYear->id, 404);

        DB::transaction(function () use ($academicYear, $semester) {
            AcademicYear::query()->update(['is_active' => false]);
            Semester::query()->update(['is_active' => false]);
            $academicYear->update(['is_active' => true]);
            $semester->update(['is_active' => true]);
        });

        return back()->with('success', 'Tahun ajaran dan semester aktif berhasil diubah.');
    }
}
