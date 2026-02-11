<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Redirect ke login jika user belum login
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Tolak akses jika role user tidak sesuai
        if (Auth::user()->role->role_name !== $role) {
            abort(403);
        }

        // Lanjutkan request jika role sesuai
        return $next($request);
    }
}
