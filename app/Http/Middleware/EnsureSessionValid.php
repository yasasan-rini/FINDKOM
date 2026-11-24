<?php

namespace App\Http\Middleware;

use App\Models\SesiPengguna;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSessionValid
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            $sid = $request->session()->getId();

            // Cari sesi berdasarkan SID + userId (production)
            $sesi = SesiPengguna::where('sid', $sid)
                ->where('userId', $user->id)
                ->first();

            // Fallback: kalau SID tidak match (testing), cari berdasarkan userId saja
            if (!$sesi) {
                $sesi = SesiPengguna::where('userId', $user->id)->first();
            }

            // Kalau ada record sesi dan sudah expired, logout
            if ($sesi && Carbon::parse($sesi->expiredAt)->isPast()) {
                SesiPengguna::where('userId', $user->id)->delete();
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