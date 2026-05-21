<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePembeliRole
{
    /**
     * Jika user yang sudah login adalah karyawan/pemilik,
     * langsung arahkan ke dashboard mereka — bukan halaman toko.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $role = Auth::user()->role;

            if (in_array($role, ['karyawan', 'pemilik'])) {
                return redirect()->route('karyawan.dashboard');
            }
        }

        return $next($request);
    }
}