<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ParentUser\ChildController as ParentChildController;
use App\Http\Controllers\ParentUser\DashboardController as ParentDashboardController;
use App\Http\Controllers\SchoolAdmin\AcademicYearController;
use App\Http\Controllers\SchoolAdmin\DashboardController as SchoolAdminDashboardController;
use App\Http\Controllers\SchoolAdmin\ParentController;
use App\Http\Controllers\SchoolAdmin\ScheduleController;
use App\Http\Controllers\SchoolAdmin\SchoolClassController;
use App\Http\Controllers\SchoolAdmin\StudentController as SchoolAdminStudentController;
use App\Http\Controllers\SchoolAdmin\SubjectController;
use App\Http\Controllers\SchoolAdmin\TeacherController as SchoolAdminTeacherController;
use App\Http\Controllers\SchoolAdmin\TeachingAssignmentController;
use App\Http\Controllers\Student\AssignmentController as StudentAssignmentController;
use App\Http\Controllers\Student\AttendanceController as StudentAttendanceController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\MaterialController as StudentMaterialController;
use App\Http\Controllers\Teacher\AssignmentController as TeacherAssignmentController;
use App\Http\Controllers\Teacher\AttendanceController as TeacherAttendanceController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\MaterialController as TeacherMaterialController;
use App\Http\Controllers\WebsiteAdmin\DashboardController as WebsiteAdminDashboardController;
use App\Http\Controllers\WebsiteAdmin\SchoolController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware(['auth', 'active.user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/files/materials/{material}', [FileController::class, 'material'])->name('files.materials');
    Route::get('/files/assignments/{assignment}', [FileController::class, 'assignment'])->name('files.assignments');
    Route::get('/files/submissions/{submission}', [FileController::class, 'submission'])->name('files.submissions');

    Route::prefix('website-admin')->name('website-admin.')->middleware('role:website_admin')->group(function () {
        Route::get('/dashboard', [WebsiteAdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/schools', [SchoolController::class, 'index'])->name('schools.index');
        Route::get('/schools/create', [SchoolController::class, 'create'])->name('schools.create');
        Route::post('/schools', [SchoolController::class, 'store'])->name('schools.store');
        Route::get('/schools/{school}', [SchoolController::class, 'show'])->name('schools.show');
        Route::get('/schools/{school}/edit', [SchoolController::class, 'edit'])->name('schools.edit');
        Route::put('/schools/{school}', [SchoolController::class, 'update'])->name('schools.update');
        Route::patch('/schools/{school}/status', [SchoolController::class, 'toggleStatus'])->name('schools.status');
    });

    Route::prefix('school-admin')->name('school-admin.')->middleware(['role:school_admin', 'school.tenant'])->group(function () {
        Route::get('/dashboard', [SchoolAdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/academic-years', [AcademicYearController::class, 'index'])->name('academic-years.index');
        Route::get('/academic-years/create', [AcademicYearController::class, 'create'])->name('academic-years.create');
        Route::post('/academic-years', [AcademicYearController::class, 'store'])->name('academic-years.store');
        Route::get('/academic-years/{academicYear}/edit', [AcademicYearController::class, 'edit'])->name('academic-years.edit');
        Route::put('/academic-years/{academicYear}', [AcademicYearController::class, 'update'])->name('academic-years.update');
        Route::patch('/academic-years/{academicYear}/semesters/{semester}/activate', [AcademicYearController::class, 'activateSemester'])->name('academic-years.semesters.activate');

        Route::get('/teachers', [SchoolAdminTeacherController::class, 'index'])->name('teachers.index');
        Route::get('/teachers/create', [SchoolAdminTeacherController::class, 'create'])->name('teachers.create');
        Route::post('/teachers', [SchoolAdminTeacherController::class, 'store'])->name('teachers.store');
        Route::get('/teachers/{teacher}/edit', [SchoolAdminTeacherController::class, 'edit'])->name('teachers.edit');
        Route::put('/teachers/{teacher}', [SchoolAdminTeacherController::class, 'update'])->name('teachers.update');
        Route::patch('/teachers/{teacher}/status', [SchoolAdminTeacherController::class, 'toggleStatus'])->name('teachers.status');

        Route::get('/students', [SchoolAdminStudentController::class, 'index'])->name('students.index');
        Route::get('/students/create', [SchoolAdminStudentController::class, 'create'])->name('students.create');
        Route::post('/students', [SchoolAdminStudentController::class, 'store'])->name('students.store');
        Route::get('/students/{student}/edit', [SchoolAdminStudentController::class, 'edit'])->name('students.edit');
        Route::put('/students/{student}', [SchoolAdminStudentController::class, 'update'])->name('students.update');
        Route::patch('/students/{student}/status', [SchoolAdminStudentController::class, 'toggleStatus'])->name('students.status');

        Route::get('/parents', [ParentController::class, 'index'])->name('parents.index');
        Route::get('/parents/create', [ParentController::class, 'create'])->name('parents.create');
        Route::post('/parents', [ParentController::class, 'store'])->name('parents.store');
        Route::get('/parents/{parent}/edit', [ParentController::class, 'edit'])->name('parents.edit');
        Route::put('/parents/{parent}', [ParentController::class, 'update'])->name('parents.update');
        Route::patch('/parents/{parent}/status', [ParentController::class, 'toggleStatus'])->name('parents.status');

        Route::get('/classes', [SchoolClassController::class, 'index'])->name('classes.index');
        Route::get('/classes/create', [SchoolClassController::class, 'create'])->name('classes.create');
        Route::post('/classes', [SchoolClassController::class, 'store'])->name('classes.store');
        Route::get('/classes/{class}/edit', [SchoolClassController::class, 'edit'])->name('classes.edit');
        Route::put('/classes/{class}', [SchoolClassController::class, 'update'])->name('classes.update');
        Route::patch('/classes/{class}/status', [SchoolClassController::class, 'toggleStatus'])->name('classes.status');
        Route::get('/classes/{class}/students', [SchoolClassController::class, 'students'])->name('classes.students');
        Route::put('/classes/{class}/students', [SchoolClassController::class, 'syncStudents'])->name('classes.students.sync');

        Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
        Route::get('/subjects/create', [SubjectController::class, 'create'])->name('subjects.create');
        Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
        Route::get('/subjects/{subject}/edit', [SubjectController::class, 'edit'])->name('subjects.edit');
        Route::put('/subjects/{subject}', [SubjectController::class, 'update'])->name('subjects.update');
        Route::patch('/subjects/{subject}/status', [SubjectController::class, 'toggleStatus'])->name('subjects.status');

        Route::get('/teaching-assignments', [TeachingAssignmentController::class, 'index'])->name('teaching-assignments.index');
        Route::get('/teaching-assignments/create', [TeachingAssignmentController::class, 'create'])->name('teaching-assignments.create');
        Route::post('/teaching-assignments', [TeachingAssignmentController::class, 'store'])->name('teaching-assignments.store');
        Route::get('/teaching-assignments/{teachingAssignment}/edit', [TeachingAssignmentController::class, 'edit'])->name('teaching-assignments.edit');
        Route::put('/teaching-assignments/{teachingAssignment}', [TeachingAssignmentController::class, 'update'])->name('teaching-assignments.update');
        Route::patch('/teaching-assignments/{teachingAssignment}/status', [TeachingAssignmentController::class, 'toggleStatus'])->name('teaching-assignments.status');

        Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
        Route::get('/schedules/create', [ScheduleController::class, 'create'])->name('schedules.create');
        Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
        Route::get('/schedules/{schedule}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');
        Route::put('/schedules/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update');
        Route::patch('/schedules/{schedule}/status', [ScheduleController::class, 'toggleStatus'])->name('schedules.status');
    });

    Route::prefix('teacher')->name('teacher.')->middleware(['role:teacher', 'school.tenant'])->group(function () {
        Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
        Route::get('/attendance', [TeacherAttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/{schedule}', [TeacherAttendanceController::class, 'show'])->name('attendance.show');
        Route::post('/attendance/{schedule}', [TeacherAttendanceController::class, 'store'])->name('attendance.store');

        Route::get('/materials', [TeacherMaterialController::class, 'index'])->name('materials.index');
        Route::get('/materials/create', [TeacherMaterialController::class, 'create'])->name('materials.create');
        Route::post('/materials', [TeacherMaterialController::class, 'store'])->name('materials.store');
        Route::get('/materials/{material}/edit', [TeacherMaterialController::class, 'edit'])->name('materials.edit');
        Route::put('/materials/{material}', [TeacherMaterialController::class, 'update'])->name('materials.update');
        Route::delete('/materials/{material}', [TeacherMaterialController::class, 'destroy'])->name('materials.destroy');

        Route::get('/assignments', [TeacherAssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/create', [TeacherAssignmentController::class, 'create'])->name('assignments.create');
        Route::post('/assignments', [TeacherAssignmentController::class, 'store'])->name('assignments.store');
        Route::get('/assignments/{assignment}/edit', [TeacherAssignmentController::class, 'edit'])->name('assignments.edit');
        Route::put('/assignments/{assignment}', [TeacherAssignmentController::class, 'update'])->name('assignments.update');
        Route::get('/assignments/{assignment}/submissions', [TeacherAssignmentController::class, 'submissions'])->name('assignments.submissions');
        Route::patch('/assignments/{assignment}/submissions/{submission}/grade', [TeacherAssignmentController::class, 'grade'])->name('assignments.grade');
        Route::delete('/assignments/{assignment}', [TeacherAssignmentController::class, 'destroy'])->name('assignments.destroy');
    });

    Route::prefix('student')->name('student.')->middleware(['role:student', 'school.tenant'])->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/attendance', [StudentAttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/materials', [StudentMaterialController::class, 'index'])->name('materials.index');
        Route::get('/assignments', [StudentAssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/{assignment}', [StudentAssignmentController::class, 'show'])->name('assignments.show');
        Route::post('/assignments/{assignment}/submit', [StudentAssignmentController::class, 'submit'])->name('assignments.submit');
    });

    Route::prefix('parent')->name('parent.')->middleware(['role:parent', 'school.tenant'])->group(function () {
        Route::get('/dashboard', [ParentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/children/{student}', [ParentChildController::class, 'show'])->name('children.show');
    });
});
