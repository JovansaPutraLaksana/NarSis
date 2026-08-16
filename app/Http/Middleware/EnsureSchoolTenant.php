<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSchoolTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->school_id) {
            abort(403, 'Akun ini belum terhubung dengan sekolah.');
        }

        if (! $user->school) {
            abort(403, 'Data sekolah tidak ditemukan.');
        }

        if (! $user->school->is_active) {
            abort(403, 'Sekolah sedang tidak aktif.');
        }

        return $next($request);
    }
}