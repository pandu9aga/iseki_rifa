<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use Illuminate\Support\Str;

class ResetEmployeePasswords extends Command
{
    protected $signature = 'employee:reset-passwords';
    protected $description = 'Reset and generate new unique 3-digit passwords for all employees';

    public function handle()
    {
        // Hapus semua password lama
        Employee::whereNotNull('password')->update(['password' => null]);

        $this->info('All existing passwords have been cleared.');

        // Ambil semua karyawan
        $employees = Employee::all();

        if ($employees->isEmpty()) {
            $this->info('No employees found.');
            return;
        }

        $usedPasswords = collect(); // Simpan password yang sudah digunakan

        foreach ($employees as $employee) {
            do {
                $password = Str::lower(Str::random(3)); // 3 karakter acak huruf dan angka, lowercase
            } while ($usedPasswords->contains($password) || Employee::where('password', $password)->exists());

            $usedPasswords->push($password);

            $employee->update(['password' => $password]);
            $this->info("Employee: {$employee->nama} ({$employee->nik}) -> Password: {$password}");
        }

        $this->info('All passwords have been reset and generated successfully.');
    }
}