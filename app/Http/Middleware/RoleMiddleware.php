<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Otorisasi sisi server berdasarkan role di database (bukan dari request).
     * Pemakaian: ->middleware('role:admin') atau 'role:admin,marketing'.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => 'Akun Anda nonaktif. Hubungi Admin.']);
        }

        if (! $user->hasRole(...$roles)) {
            return redirect()->to($user->dashboardUrl())
                ->with('toast_error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
