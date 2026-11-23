<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegistrasiController extends Controller
{
    public function form() { return view('auth.register'); }

    // PC001 - registrasi(data: RegistrasiDTO)
    public function registrasi(Request $request)
    {
        $validated = $request->validate([
            'namaLengkap' => 'required|string|min:3|max:100',
            'nim' => 'required|digits_between:10,20|unique:User,nim',
            'email' => ['required', 'email', 'max:100', 'unique:User,email', 'regex:/@(student\.)?ub\.ac\.id$/i'],
            'username' => 'required|string|min:4|max:50|unique:User,username',
            'password' => 'required|string|min:8',
            'kontak' => ['required', 'regex:/^0\d{8,13}$/'],
        ], [
            'email.regex' => 'Gunakan email institusi UB (@student.ub.ac.id / @ub.ac.id).',
            'email.unique' => 'Akun dengan email ini sudah terdaftar.',
            'nim.unique' => 'NIM/NIP sudah terdaftar.',
            'username.unique' => 'Username sudah dipakai.',
            'kontak.regex' => 'Nomor WhatsApp tidak valid (awali 0).',
        ]);

        User::create([
            'namaLengkap' => $validated['namaLengkap'],
            'nim' => $validated['nim'],
            'email' => strtolower($validated['email']),
            'username' => $validated['username'],
            'passwordHash' => Hash::make($validated['password']),
            'kontak' => $validated['kontak'],
            'role' => 'CIVITAS',
        ]);

        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil. Silakan login.')
            ->with('email', strtolower($validated['email']));
    }
}