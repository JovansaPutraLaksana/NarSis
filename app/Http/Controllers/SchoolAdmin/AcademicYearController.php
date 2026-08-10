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
        $academicYears = AcademicYear::query()
            ->with('semesters')
            ->latest('start_date')
            ->get();

        return view(
            'school-admin.academic-years.index',
            compact('academicYears')
        );
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
                'required',
                'string',
                'max:20',

                Rule::unique('academic_years', 'name')
                    ->where(
                        fn ($query) =>
                        $query->where('school_id', $schoolId)
                    ),
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'required',
                'date',
                'after:start_date',
            ],
        ]);


        DB::transaction(function () use ($validated) {

            $academicYear = AcademicYear::create([
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_active' => false,
            ]);


            /*
             * Untuk sementara tanggal semester
             * mengikuti tahun ajaran.
             *
             * Nantinya dapat kita ubah melalui menu edit.
             */

            Semester::create([
                'academic_year_id' => $academicYear->id,
                'name' => 'Ganjil',
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_active' => false,
            ]);


            Semester::create([
                'academic_year_id' => $academicYear->id,
                'name' => 'Genap',
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_active' => false,
            ]);

        });


        return redirect()
            ->route('school-admin.academic-years.index')
            ->with(
                'success',
                'Tahun ajaran berhasil dibuat.'
            );
    }


    public function activate(AcademicYear $academicYear)
    {
        DB::transaction(function () use ($academicYear) {

            AcademicYear::query()
                ->update([
                    'is_active' => false,
                ]);

            Semester::query()
                ->update([
                    'is_active' => false,
                ]);

            $academicYear->update([
                'is_active' => true,
            ]);

        });


        return back()->with(
            'success',
            'Tahun ajaran berhasil diaktifkan.'
        );
    }


    public function activateSemester(
        AcademicYear $academicYear,
        Semester $semester
    ) {
        abort_if(
            $semester->academic_year_id !== $academicYear->id,
            404
        );


        DB::transaction(function () use (
            $academicYear,
            $semester
        ) {

            AcademicYear::query()
                ->update([
                    'is_active' => false,
                ]);

            Semester::query()
                ->update([
                    'is_active' => false,
                ]);

            $academicYear->update([
                'is_active' => true,
            ]);

            $semester->update([
                'is_active' => true,
            ]);

        });


        return back()->with(
            'success',
            'Semester aktif berhasil diubah.'
        );
    }
}