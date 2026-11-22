<?php

namespace App\Http\Controllers;

use App\Models\SesiPengguna;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Throwable;

class LoginController extends Controller
{
    public function bukaHalamanLogin()
    {
        return view('auth.login');
    }

    public function autentikasi(Request $request)
    {
        $kredensial = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Semua field wajib diisi',
            'password.required' => 'Semua field wajib diisi',
        ]);

        try {
            $user = User::where('username', $kredensial['login'])
                ->orWhere('email', $kredensial['login'])
                ->first();

            if (!$user || !Hash::check($kredensial['password'], $user->passwordHash)) {
                return back()
                    ->withInput($request->only('login'))
                    ->with('error', 'Username/email atau password salah');
            }

            $hakAkses = $user->role;

            Auth::login($user);
            $request->session()->regenerate();

            SesiPengguna::create([
                'sid' => $request->session()->getId(),
                'userId' => $user->id,
                'hakAkses' => $hakAkses,
                'createdAt' => now(),
                'expiredAt' => now()->addHours(12),
            ]);

        } catch (Throwable $e) {
            report($e);
            return back()->with('error', 'Proses login gagal');
        }

        $tujuan = $hakAkses === 'SECURITY' ? 'security.dashboard' : 'civitas.home';
        return redirect()->route($tujuan)->with('success', 'Login berhasil');
    }
}