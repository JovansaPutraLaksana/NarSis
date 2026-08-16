<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\AcademicYear;
use App\Models\Assignment;
use App\Models\ClassEnrollment;
use App\Models\Material;
use App\Models\ParentProfile;
use App\Models\Schedule;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $school = School::updateOrCreate(
                ['code' => 'DEMO01'],
                [
                    'name' => 'SMA NarSis Demo',
                    'address' => 'Palembang',
                    'phone' => '0711000000',
                    'email' => 'sekolah.demo@narsis.test',
                    'is_active' => true,
                ]
            );

            $schoolAdmin = User::updateOrCreate(
                ['email' => 'schooladmin@narsis.test'],
                [
                    'school_id' => $school->id,
                    'name' => 'Admin Sekolah Demo',
                    'password' => 'password123',
                    'role' => UserRole::SchoolAdmin,
                    'is_active' => true,
                ]
            );

            $teacherUser = User::updateOrCreate(
                ['email' => 'guru@narsis.test'],
                [
                    'school_id' => $school->id,
                    'name' => 'Budi Santoso',
                    'password' => 'password123',
                    'role' => UserRole::Teacher,
                    'is_active' => true,
                ]
            );

            $teacher = TeacherProfile::updateOrCreate(
                ['user_id' => $teacherUser->id],
                [
                    'school_id' => $school->id,
                    'employee_number' => 'GR001',
                    'nip' => '198501012010011001',
                    'gender' => 'L',
                    'phone' => '081200000001',
                    'address' => 'Palembang',
                ]
            );

            $studentUser = User::updateOrCreate(
                ['email' => 'siswa@narsis.test'],
                [
                    'school_id' => $school->id,
                    'name' => 'Andi Pratama',
                    'password' => 'password123',
                    'role' => UserRole::Student,
                    'is_active' => true,
                ]
            );

            $student = StudentProfile::updateOrCreate(
                ['user_id' => $studentUser->id],
                [
                    'school_id' => $school->id,
                    'nis' => 'S0001',
                    'nisn' => '0012345678',
                    'gender' => 'L',
                    'birth_place' => 'Palembang',
                    'birth_date' => '2010-05-15',
                    'phone' => '081200000002',
                    'address' => 'Palembang',
                ]
            );

            $parentUser = User::updateOrCreate(
                ['email' => 'orangtua@narsis.test'],
                [
                    'school_id' => $school->id,
                    'name' => 'Orang Tua Andi',
                    'password' => 'password123',
                    'role' => UserRole::Parent,
                    'is_active' => true,
                ]
            );

            $parent = ParentProfile::updateOrCreate(
                ['user_id' => $parentUser->id],
                [
                    'school_id' => $school->id,
                    'phone' => '081200000003',
                    'address' => 'Palembang',
                ]
            );

            DB::table('parent_student')->updateOrInsert(
                [
                    'parent_profile_id' => $parent->id,
                    'student_profile_id' => $student->id,
                ],
                [
                    'school_id' => $school->id,
                    'relationship' => 'Ayah',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $academicYear = AcademicYear::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'name' => '2026/2027',
                ],
                [
                    'start_date' => '2026-07-01',
                    'end_date' => '2027-06-30',
                    'is_active' => true,
                ]
            );

            $semesterOdd = Semester::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'academic_year_id' => $academicYear->id,
                    'name' => 'Ganjil',
                ],
                [
                    'start_date' => '2026-07-01',
                    'end_date' => '2026-12-31',
                    'is_active' => true,
                ]
            );

            Semester::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'academic_year_id' => $academicYear->id,
                    'name' => 'Genap',
                ],
                [
                    'start_date' => '2027-01-01',
                    'end_date' => '2027-06-30',
                    'is_active' => false,
                ]
            );

            $class = SchoolClass::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'academic_year_id' => $academicYear->id,
                    'name' => 'X IPA 1',
                ],
                [
                    'homeroom_teacher_id' => $teacher->id,
                    'grade_level' => '10',
                    'room' => 'Ruang 10A',
                    'is_active' => true,
                ]
            );

            ClassEnrollment::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'academic_year_id' => $academicYear->id,
                    'student_profile_id' => $student->id,
                ],
                [
                    'school_class_id' => $class->id,
                    'enrolled_at' => '2026-07-01',
                    'status' => 'active',
                ]
            );

            $subject = Subject::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'code' => 'MAT',
                ],
                [
                    'name' => 'Matematika',
                    'description' => 'Mata pelajaran demo NarSis.',
                    'is_active' => true,
                ]
            );

            $teaching = TeachingAssignment::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'semester_id' => $semesterOdd->id,
                    'school_class_id' => $class->id,
                    'subject_id' => $subject->id,
                ],
                [
                    'teacher_profile_id' => $teacher->id,
                    'is_active' => true,
                ]
            );

            Schedule::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'semester_id' => $semesterOdd->id,
                    'teaching_assignment_id' => $teaching->id,
                    'day_of_week' => now()->dayOfWeekIso,
                ],
                [
                    'start_time' => '00:00:00',
                    'end_time' => '23:59:00',
                    'room' => 'Ruang 10A',
                    'is_active' => true,
                ]
            );

            Material::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'teaching_assignment_id' => $teaching->id,
                    'title' => 'Materi Demo: Persamaan Linear',
                ],
                [
                    'teacher_profile_id' => $teacher->id,
                    'description' => 'Materi demo tanpa lampiran untuk menguji alur siswa.',
                    'published_at' => now(),
                ]
            );

            Assignment::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'teaching_assignment_id' => $teaching->id,
                    'title' => 'Tugas Demo Matematika',
                ],
                [
                    'teacher_profile_id' => $teacher->id,
                    'description' => 'Kerjakan contoh soal persamaan linear dan unggah jawaban.',
                    'due_at' => now()->addDays(7),
                    'max_score' => 100,
                    'published_at' => now(),
                ]
            );

            // Keep variable referenced so static analyzers do not flag it as accidental.
            unset($schoolAdmin);
        });
    }
}
