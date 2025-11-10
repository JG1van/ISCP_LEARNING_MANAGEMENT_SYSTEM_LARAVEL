<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Jika belum login, arahkan ke halaman login
        if (!$request->session()->has('admin_id')) {
            return redirect()->route('login')->with('status', 'Silakan login terlebih dahulu.');
        }

        // Jika sudah login, lanjut ke halaman berikutnya
        return $next($request);
    }
}
