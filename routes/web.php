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

use App\Models\Lembur;
use Illuminate\Support\Facades\Route;

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

// Report
Route::get('/reporting', [ReportingController::class, 'read'])->name('reporting')->middleware('auth');
Route::get('/reporting/create', [ReportingController::class, 'create']);
Route::post('/reporting', [ReportingController::class, 'store'])->name('reporting.store');
Route::get('/employee/details', [ReportingController::class, 'getEmployeeDetails'])->name('employee.details')->middleware('auth');
Route::get('/reporting/export', [ReportingController::class, 'export']);
Route::put('/reporting/{id}', [ReportingController::class, 'update'])->name('reporting.update');
Route::delete('/reporting/{id}', [ReportingController::class, 'destroy'])->name('reporting.destroy');
Route::put('/reporting/{id}/approve', [ReportingController::class, 'approve'])->name('reporting.approve')->middleware('auth');
Route::get('/reporting/daily-report', [ReportingController::class, 'dailyReport']);
Route::post('/reporting/nihil', [ReportingController::class, 'storeNihil'])->name('reporting.nihil');
Route::get('/reporting/download-pdf', [ReportingController::class, 'pdf'])->name('reporting.pdf');
Route::get('/reporting/download-excel', [ReportingController::class, 'excel'])->name('reporting.excel');

// Employees
Route::get('/employees', [EmployeeController::class, 'read'])->name('employees.read')->middleware('auth');
Route::get('/employees/new', [EmployeeController::class, 'create'])->middleware('auth');
Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store')->middleware('auth');
Route::put('/employees/{id}', [EmployeeController::class, 'update'])->middleware('auth');
Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy')->middleware('auth');

// Users
Route::get('/users', [UserController::class, 'read'])->name('users.read')->middleware('auth');
Route::get('/users/new', [UserController::class, 'create'])->middleware('auth');
Route::post('/users', [UserController::class, 'store'])->name('users.store')->middleware('auth');
Route::put('/users/{id}', [UserController::class, 'update'])->middleware('auth');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('auth');

// Special Dates
Route::get('/dates', [DateController::class, 'read'])->name('dates.read')->middleware('auth');
Route::get('/dates/new', [DateController::class, 'create'])->middleware('auth');
Route::post('/dates', [DateController::class, 'store'])->name('dates.store')->middleware('auth');
Route::put('/dates/{id}', [DateController::class, 'update'])->middleware('auth');
Route::delete('/dates/{id}', [DateController::class, 'destroy'])->name('dates.destroy')->middleware('auth');

// Pengganti
Route::get('/replacements', [ReplacementController::class, 'read'])->name('replacements.read');
Route::get('/replacements/create/{id}', [ReplacementController::class, 'create'])->name('replacements.create');
Route::get('/employee/by-nik/{nik}', function ($nik) {
    $employee = \App\Models\Employee::where('nik', $nik)->first();
    return response()->json($employee);
});

Route::get('/lembur', [LemburController::class, 'index'])->name('lemburs.index');   // List
Route::get('/lembur/create', [LemburController::class, 'create'])->name('lemburs.create'); // Form tambah
Route::post('/lembur', [LemburController::class, 'store'])->name('lemburs.store');  // Simpan
Route::delete('/lembur/{id}', [LemburController::class, 'destroy'])->name('lemburs.destroy');  // Hapus
Route::put('/lembur/{id}', [LemburController::class, 'update'])->name('lemburs.update');  // Edit
Route::get('/export-lembur', [LemburController::class, 'exportLembur'])->name('export.lembur');
Route::put('/lembur/{id}/approve', [LemburController::class, 'approve'])->name('lembur.approve');


Route::prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/lembur', [LaporanLemburController::class, 'index'])
        ->name('lembur.index');

    Route::get('/lembur/export', [LaporanLemburController::class, 'export'])
        ->name('lembur.export');

}); //     'index', 'store', 'edit', 'update', 'destroy'
// ]);

Route::post('/replacements/store', [ReplacementController::class, 'store'])->name('replacements.store');
Route::get('/replacements/by-absensi/{id}', [ReplacementController::class, 'byAbsensi']);
