<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\WebsiteAdmin\DashboardController as WebsiteAdminDashboardController;
use App\Http\Controllers\SchoolAdmin\DashboardController as SchoolAdminDashboardController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\ParentUser\DashboardController as ParentDashboardController;

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

        });


    // =============================
    // SCHOOL ADMIN
    // =============================

    Route::prefix('school-admin')
        ->name('school-admin.')
        ->middleware('role:school_admin')
        ->group(function () {

            Route::get('/dashboard', [SchoolAdminDashboardController::class, 'index'])
                ->name('dashboard');

        });


    // =============================
    // TEACHER
    // =============================

    Route::prefix('teacher')
        ->name('teacher.')
        ->middleware('role:teacher')
        ->group(function () {

            Route::get('/dashboard', [TeacherDashboardController::class, 'index'])
                ->name('dashboard');

        });


    // =============================
    // STUDENT
    // =============================

    Route::prefix('student')
        ->name('student.')
        ->middleware('role:student')
        ->group(function () {

            Route::get('/dashboard', [StudentDashboardController::class, 'index'])
                ->name('dashboard');

        });


    // =============================
    // PARENT
    // =============================

    Route::prefix('parent')
        ->name('parent.')
        ->middleware('role:parent')
        ->group(function () {

            Route::get('/dashboard', [ParentDashboardController::class, 'index'])
                ->name('dashboard');

        });

});