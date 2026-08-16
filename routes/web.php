<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ParentUser\DashboardController as ParentDashboardController;
use App\Http\Controllers\SchoolAdmin\AcademicYearController;
use App\Http\Controllers\SchoolAdmin\DashboardController as SchoolAdminDashboardController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\WebsiteAdmin\DashboardController as WebsiteAdminDashboardController;
use App\Http\Controllers\WebsiteAdmin\SchoolController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// =============================
// GUEST
// =============================

Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class, 'create'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'store'])
        ->name('login.store');
});

// =============================
// AUTHENTICATED USER
// =============================

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::post('/logout', [LoginController::class, 'destroy'])
        ->name('logout');

    // =============================
    // WEBSITE ADMIN
    // =============================

    Route::prefix('website-admin')
        ->name('website-admin.')
        ->middleware('role:website_admin')
        ->group(function () {

            Route::get('/dashboard', [WebsiteAdminDashboardController::class, 'index'])
                ->name('dashboard');

            Route::get(
                '/schools',
                [SchoolController::class, 'index']
            )->name('schools.index');

            Route::get(
                '/schools/create',
                [SchoolController::class, 'create']
            )->name('schools.create');

            Route::post(
                '/schools',
                [SchoolController::class, 'store']
            )->name('schools.store');

            Route::get(
                '/schools/{school}',
                [SchoolController::class, 'show']
            )->name('schools.show');
        });

    // =============================
    // SCHOOL ADMIN
    // =============================

    Route::prefix('school-admin')
        ->name('school-admin.')
        ->middleware(['role:school_admin', 'school.tenant'])
        ->group(function () {

            Route::get('/dashboard', [SchoolAdminDashboardController::class, 'index'])
                ->name('dashboard');

            Route::get(
                '/academic-years',
                [AcademicYearController::class, 'index']
            )->name('academic-years.index');

            Route::get(
                '/academic-years/create',
                [AcademicYearController::class, 'create']
            )->name('academic-years.create');

            Route::post(
                '/academic-years',
                [AcademicYearController::class, 'store']
            )->name('academic-years.store');

            Route::patch(
                '/academic-years/{academicYear}/activate',
                [AcademicYearController::class, 'activate']
            )->name('academic-years.activate');

            Route::patch(
                '/academic-years/{academicYear}/semesters/{semester}/activate',
                [AcademicYearController::class, 'activateSemester']
            )->name('academic-years.semesters.activate');
        });

    // =============================
    // TEACHER
    // =============================

    Route::prefix('teacher')
        ->name('teacher.')
        ->middleware(['role:teacher', 'school.tenant'])
        ->group(function () {

            Route::get('/dashboard', [TeacherDashboardController::class, 'index'])
                ->name('dashboard');
        });

    // =============================
    // STUDENT
    // =============================

    Route::prefix('student')
        ->name('student.')
        ->middleware(['role:student', 'school.tenant'])
        ->group(function () {

            Route::get('/dashboard', [StudentDashboardController::class, 'index'])
                ->name('dashboard');
        });

    // =============================
    // PARENT
    // =============================

    Route::prefix('parent')
        ->name('parent.')
        ->middleware(['role:parent', 'school.tenant'])
        ->group(function () {

            Route::get('/dashboard', [ParentDashboardController::class, 'index'])
                ->name('dashboard');
        });
});
