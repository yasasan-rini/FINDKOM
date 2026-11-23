<?php

namespace App\Http\Middleware;

use App\Models\SesiPengguna;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class EnsureSessionValid
{
    /** Sesi login berlaku 12 jam (Session.expiredAt). */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user) {
            $sid = $request->session()->getId();
            $sesi = SesiPengguna::where('sid', $sid)->where('userId', $user->id)->first();

            if (! $sesi || Carbon::parse($sesi->expiredAt)->isPast()) {
                SesiPengguna::where('sid', $sid)->delete();
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', 'Sesi login telah berakhir. Silakan masuk kembali.');
            }
        }
        return $next($request);
    }
}