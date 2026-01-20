<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function showLogin()
    {
        // Cek apakah user sudah login sebagai employee
        if (session()->has('employee_login') && session('employee_login')) {
            return redirect()->route('employee.reporting');
        }

        // Cek apakah user sudah login sebagai user biasa
        if (Auth::check()) {
            return redirect()->route('reporting');
        }

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

        // Cek login ke User (default)
        if (Auth::attempt($validated)) {
            $request->session()->regenerate();

            // Log
            activity('auth')
                ->causedBy(Auth::user())
                ->withProperties(['username' => Auth::user()->username])
                ->log(Auth::user()->name . ' berhasil login');

            return redirect()->route('reporting');
        }

        // Jika login ke User gagal, coba login ke Employee
        $employee = Employee::where('nik', $validated['username'])->first();

        if ($employee && $validated['password'] === $employee->password) {
            // Simulasikan login sebagai user dengan tipe employee
            $user = new \stdClass();
            $user->id = $employee->id;
            $user->name = $employee->nama;
            $user->username = $employee->nik;
            $user->type = 'employee';
            $user->email = null;

            // Gunakan session untuk menyimpan data login employee
            $request->session()->put('employee_login', true);
            $request->session()->put('employee_user', $user);
            $request->session()->regenerate();

            // Log
            activity('auth')
                ->withProperties(['username' => $employee->nik])
                ->log($employee->nama . ' berhasil login sebagai employee');

            return redirect()->route('reporting');
        }

        throw ValidationException::withMessages([
            'credentials' => 'Username atau password yang Anda masukkan tidak sesuai.'
        ]);
    }

    public function logout(Request $request)
    {
        // Cek apakah login sebagai employee
        if (session()->has('employee_login') && session('employee_login')) {
            $employeeUser = session('employee_user');

            // Log
            activity('auth')
                ->withProperties(['username' => $employeeUser->username])
                ->log("{$employeeUser->name} telah logout dari sistem");

            // Hapus session employee
            $request->session()->forget('employee_login');
            $request->session()->forget('employee_user');
            $request->session()->regenerate();

            return redirect()->route('show.login');
        }

        // Logout user biasa
        $user = Auth::user();

        // Log
        activity('auth')
            ->causedBy($user)
            ->withProperties(['username' => $user->username])
            ->log("{$user->name} telah logout dari sistem");

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('show.login');
    }
}
