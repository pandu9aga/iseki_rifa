<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use Illuminate\Support\Str;

class GenerateEmployeePasswords extends Command
{
    protected $signature = 'employee:generate-passwords';
    protected $description = 'Generate unique 3-digit passwords for all employees without password';

    public function handle()
    {
        $employees = Employee::whereNull('password')->get();

        if ($employees->isEmpty()) {
            $this->info('All employees already have passwords.');
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

        $this->info('All passwords generated successfully.');
    }
}