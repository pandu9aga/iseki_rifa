<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ReportingController::class, 'read'])->name('index');
Route::get('/register', [AuthController::class, 'showRegister'])->name('show.register');
Route::get('/login', [AuthController::class, 'showLogin'])->name('show.login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Report
Route::get('/reporting', [ReportingController::class, 'create']);
Route::post('/reporting', [ReportingController::class, 'store'])->name('reporting.store');
Route::get('/employee/details', [ReportingController::class, 'getEmployeeDetails'])->name('employee.details');
Route::get('/reporting/export', [ReportingController::class, 'export']);
Route::put('/reporting/{id}', [ReportingController::class, 'update'])->name('reporting.update');
Route::delete('/reporting/{id}', [ReportingController::class, 'destroy'])->name('reporting.destroy');
Route::put('/reporting/{id}/approve', [ReportingController::class, 'approve'])->name('reporting.approve')->middleware('auth');
Route::get('/reporting/daily-report', [ReportingController::class, 'dailyReport']);

// Employees
Route::get('/employees', [EmployeeController::class, 'read'])->name('employees.read')->middleware('auth');
Route::get('/employees/new', [EmployeeController::class, 'create'])->middleware('auth');
Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store')->middleware('auth');
Route::put('/employees/{id}', [EmployeeController::class, 'update'])->middleware('auth');
Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy')->middleware('auth');

// Users
Route::get('/users', [UserController::class, 'read'])->name('users.read')->middleware('auth');
Route::put('/users/{id}', [UserController::class, 'update'])->middleware('auth');
