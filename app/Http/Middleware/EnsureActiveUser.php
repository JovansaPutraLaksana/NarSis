<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $profileMissing = match ($user->role) {
            UserRole::Teacher => ! $user->teacherProfile,
            UserRole::Student => ! $user->studentProfile,
            UserRole::Parent => ! $user->parentProfile,
            default => false,
        };

        if (! $user->is_active || $profileMissing) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun tidak aktif atau profil pengguna belum lengkap. Hubungi administrator sekolah.',
            ]);
        }

        return $next($request);
    }
}
