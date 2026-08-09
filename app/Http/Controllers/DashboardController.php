<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return match ($user->role) {

            UserRole::WebsiteAdmin =>
                redirect()->route('website-admin.dashboard'),

            UserRole::SchoolAdmin =>
                redirect()->route('school-admin.dashboard'),

            UserRole::Teacher =>
                redirect()->route('teacher.dashboard'),

            UserRole::Student =>
                redirect()->route('student.dashboard'),

            UserRole::Parent =>
                redirect()->route('parent.dashboard'),

        };
    }
}