<?php

use App\Http\Middleware\CheckUserType;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportingController;
use App\Http\Controllers\DateController;
use App\Http\Controllers\ReplacementController;
use App\Http\Controllers\LemburController;
use App\Http\Controllers\LaporanLemburController;
use App\Http\Controllers\PenilaianTahunanController; // <-- Tambahkan ini

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Guest routes
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('reporting');
    }
    return app(AuthController::class)->showLogin();
})->name('index');

Route::get('/register', [AuthController::class, 'showRegister'])->name('show.register');
Route::get('/login', [AuthController::class, 'showLogin'])->name('show.login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Request Nuzul
Route::get('/divisioning', [EmployeeController::class, 'totalInDivisions']);

// Authenticated routes
Route::middleware('auth')->group(function () {

    // Reporting
    Route::get('/reporting', [ReportingController::class, 'read'])->name('reporting');
    Route::get('/reporting/create', [ReportingController::class, 'create']);
    Route::post('/reporting', [ReportingController::class, 'store'])->name('reporting.store');
    Route::get('/employee/details', [ReportingController::class, 'getEmployeeDetails'])->name('employee.details');
    Route::get('/reporting/export', [ReportingController::class, 'export']);
    Route::put('/reporting/{id}', [ReportingController::class, 'update'])->name('reporting.update');
    Route::delete('/reporting/{id}', [ReportingController::class, 'destroy'])->name('reporting.destroy');
    Route::put('/reporting/{id}/approve', [ReportingController::class, 'approve'])->name('reporting.approve');
    Route::get('/reporting/daily-report', [ReportingController::class, 'dailyReport']);
    Route::post('/reporting/nihil', [ReportingController::class, 'storeNihil'])->name('reporting.nihil');
    Route::get('/reporting/download-pdf', [ReportingController::class, 'pdf'])->name('reporting.pdf');
    Route::get('/reporting/download-excel', [ReportingController::class, 'excel'])->name('reporting.excel');

    // Employees
    Route::get('/employees', [EmployeeController::class, 'read'])->name('employees.read');
    Route::get('/employees/new', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    // Users
    Route::get('/users', [UserController::class, 'read'])->name('users.read');
    Route::get('/users/new', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Special Dates
    Route::get('/dates', [DateController::class, 'read'])->name('dates.read');
    Route::get('/dates/new', [DateController::class, 'create'])->name('dates.create');
    Route::post('/dates', [DateController::class, 'store'])->name('dates.store');
    Route::put('/dates/{id}', [DateController::class, 'update'])->name('dates.update');
    Route::delete('/dates/{id}', [DateController::class, 'destroy'])->name('dates.destroy');

    // Lembur
    Route::get('/lembur', [LemburController::class, 'index'])->name('lemburs.index');
    Route::get('/lembur/create', [LemburController::class, 'create'])->name('lemburs.create');
    Route::post('/lembur', [LemburController::class, 'store'])->name('lemburs.store');
    Route::put('/lembur/{id}', [LemburController::class, 'update'])->name('lemburs.update');
    Route::delete('/lembur/{id}', [LemburController::class, 'destroy'])->name('lemburs.destroy');
    Route::get('/export-lembur', [LemburController::class, 'exportLembur'])->name('export.lembur');
    Route::put('/lembur/{id}/approve', [LemburController::class, 'approve'])->name('lembur.approve');

    // Laporan Lembur
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/lembur', [LaporanLemburController::class, 'index'])->name('lembur.index');
        Route::get('/lembur/export', [LaporanLemburController::class, 'export'])->name('lembur.export');
    });

    // Pengganti
    Route::get('/replacements', [ReplacementController::class, 'read'])->name('replacements.read');
    Route::get('/replacements/create/{id}', [ReplacementController::class, 'create'])->name('replacements.create');
    Route::post('/replacements/store', [ReplacementController::class, 'store'])->name('replacements.store');
    Route::get('/replacements/by-absensi/{id}', [ReplacementController::class, 'byAbsensi']);

    // ✅ PENILAIAN TAHUNAN — BARU
    Route::prefix('penilaian')->name('penilaian.')->group(function () {
        Route::get('/', [PenilaianTahunanController::class, 'index'])->name('index');
        Route::post('/', [PenilaianTahunanController::class, 'store'])->name('store');
        Route::get('/export', [PenilaianTahunanController::class, 'export'])->name('export');
    });
});

// Helper routes (tanpa auth, tapi aman karena hanya read)
Route::get('/employee/by-nik/{nik}', function ($nik) {
    $employee = \App\Models\Employee::where('nik', $nik)->first();
    return response()->json($employee ?? []);
});
