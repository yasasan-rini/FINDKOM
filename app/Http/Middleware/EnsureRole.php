<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = $request->user();
        if (! $user || $user->role !== $role) {
            return redirect()->route($user?->role === 'SECURITY' ? 'dashboard' : 'beranda')
                ->with('error', 'Halaman tersebut tidak sesuai dengan hak akses akunmu.');
        }
        return $next($request);
    }
}