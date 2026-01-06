<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Absensi extends Model
{
    use HasFactory; //, LogsActivity;

    protected $fillable = [
        'employee_id',
        'tanggal',
        'kategori',
        'keterangan',
        'jam_masuk',
        'jam_keluar',
        'is_approved'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_approved' => 'boolean',
    ];

    // Log
    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //         ->useLogName('absensi')
    //         ->logOnly(['employee_id', 'tanggal', 'kategori', 'keterangan', 'is_approved'])
    //         ->logOnlyDirty()
    //         ->setDescriptionForEvent(function (string $eventName) {
    //             $causer = Auth::check() ? Auth::user()->name : 'Guest';
    //             $kategori = $this->kategori ?? '-';
    //             $tanggal = $this->tanggal?->format('Y-m-d') ?? '-';
    //             $employeeName = $this->employee->nama ?? '(nama tidak ditemukan)';

    //             return "{$causer} melakukan {$eventName} absensi untuk {$employeeName} pada {$tanggal} dengan kategori {$kategori}";
    //         });
    // }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function replacements()
    {
        return $this->hasMany(Replacement::class);
    }

    public function getKategoriLabelAttribute()
    {
        return match ($this->kategori) {
            'C' => 'Cuti',
            'CSP' => 'Cuti Setengah Hari Pagi',
            'CSS' => 'Cuti Setengah Hari Siang',
            'T' => 'Terlambat',
            'IK' => 'Izin Keluar',
            'P' => 'Pulang Cepat',
            'A' => 'Absen',
            'ASP' => 'Absen Setengah Hari Pagi',
            'ASS' => 'Absen Setengah Hari Siang',
            'S' => 'Sakit',
            'CK' => 'Cuti Khusus',
            'Sk' => 'Serikat',
            default => $this->kategori,
        };
    }
}
