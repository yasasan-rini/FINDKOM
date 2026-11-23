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
    public function bukaHalamanLogin() { return view('auth.login'); }

    // PC002 - autentikasi(kredensial)
    public function autentikasi(Request $request)
    {
        $kredensial = $request->validate([
            'email' => ['required', 'regex:/@(student\.)?ub\.ac\.id$/i'],
            'password' => 'required|string',
            'peran' => 'required|in:CIVITAS,SECURITY',
        ], [
            'email.required' => 'Semua field wajib diisi',
            'email.regex' => 'Gunakan email institusi UB (@student.ub.ac.id / @ub.ac.id).',
            'password.required' => 'Semua field wajib diisi',
        ]);

        try {
            $user = User::where('email', strtolower($kredensial['email']))->first();
            if (! $user || ! Hash::check($kredensial['password'], $user->passwordHash)) {
                return back()->withInput($request->only('email', 'peran'))
                    ->with('error', 'Email atau kata sandi salah.');
            }
            if ($user->role !== $kredensial['peran']) {
                $nama = $kredensial['peran'] === 'SECURITY' ? 'Security' : 'Civitas Akademika';
                return back()->withInput($request->only('email', 'peran'))
                    ->with('error', "Akun ini tidak terdaftar sebagai {$nama}.");
            }
            $this->mulaiSesi($request, $user);
        } catch (Throwable $e) {
            report($e);
            return back()->with('error', 'Proses login gagal');
        }

        return redirect()->route($user->role === 'SECURITY' ? 'dashboard' : 'beranda')
            ->with('success', 'Login berhasil. Selamat datang, '.explode(' ', $user->namaLengkap)[0].'!');
    }

    /** Tombol "Coba cepat" — akun demo hasil seeder (username tetap). */
    public function demo(Request $request, string $role)
    {
        abort_unless(in_array($role, ['CIVITAS', 'SECURITY']), 404);
        $username = $role === 'SECURITY' ? 'budisecurity' : 'rinawulan';
        $user = User::where('username', $username)->first()
            ?? User::where('role', $role)->orderBy('createdAt')->first();
        if (! $user) return back()->with('error', 'Akun demo belum ada. Jalankan php artisan migrate --seed.');

        $this->mulaiSesi($request, $user);
        return redirect()->route($role === 'SECURITY' ? 'dashboard' : 'beranda')
            ->with('success', "Masuk sebagai {$user->namaLengkap} (demo).");
    }

    private function mulaiSesi(Request $request, User $user): void
    {
        Auth::login($user);
        $request->session()->regenerate();
        SesiPengguna::updateOrCreate(['sid' => $request->session()->getId()], [
            'userId' => $user->id, 'hakAkses' => $user->role,
            'createdAt' => now(), 'expiredAt' => now()->addHours(12),
        ]);
    }

    public function keluar(Request $request)
    {
        SesiPengguna::where('sid', $request->session()->getId())->delete();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda telah keluar. Sampai jumpa!');
    }
}