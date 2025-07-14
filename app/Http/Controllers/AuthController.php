<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }
    public function showLogin()
    {
        return view('auth.login');
    }
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',

            'username.required' => 'username wajib diisi.',
            'username.unique' => 'Username sudah terdaftar.',

            'password.required' => 'Password wajib diisi.',
            'password.string' => 'Password harus berupa teks.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $validated['type'] = 'admin';
        $user = User::create($validated);
        Auth::login($user);

        return redirect()->route('index');
    }

    public function login(Request $request)
    {
        $validated = $request->validate(
            [
                'username' => 'required|string',
                'password' => 'required|string',
            ],
            [
                'username.required' => 'Username wajib diisi.',
                'password.required' => 'Password wajib diisi.',
            ]
        );

        if (Auth::attempt($validated)) {
            $request->session()->regenerate();

            // Log
            activity('auth')
                ->causedBy(Auth::user())
                ->withProperties(['username' => Auth::user()->username])
                ->log(Auth::user()->name . ' berhasil login');

            return redirect()->route('index');
        }

        throw ValidationException::withMessages([
            'credentials' => 'Email atau password yang Anda masukkan tidak sesuai.'
        ]);
    }
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Log
        activity('auth')
            ->causedBy($user)
            ->withProperties(['username' => Auth::user()->username])
            ->log("{$user->name} telah logout dari sistem");
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('index');
    }
}
