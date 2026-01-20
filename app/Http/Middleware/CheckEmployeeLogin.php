<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckEmployeeLogin
{
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user login sebagai employee
        if (session()->has('employee_login') && session('employee_login')) {
            return $next($request);
        }

        // Jika tidak, arahkan ke login
        return redirect()->route('show.login');
    }
}