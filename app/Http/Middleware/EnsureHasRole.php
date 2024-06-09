<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Ambil role pengguna yang sedang login
        $userRole = $request->user()->role;

        // Cek apakah role pengguna ada di dalam array role yang diizinkan
        if (!in_array($userRole, $roles)) {
            // Jika role pengguna tidak ada dalam daftar yang diizinkan, redirect ke halaman lain
            return redirect('/');
        }

        // Jika role pengguna ada dalam daftar yang diizinkan, lanjutkan request
        return $next($request);
    }
}
