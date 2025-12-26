<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'nik', 'team', 'division_id', 'status', 'deleted_at'];

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    public function lemburs()
    {
        return $this->hasMany(Lembur::class, 'employee_id', 'id');
    }

    // 🔸 RELASI WAJIB: nama method HARUS SAMA dengan yang dipanggil di controller
    public function nilaiTahunan()
    {
        return $this->hasMany(NilaiPegawai::class, 'employee_id');
        // TANPA whereYear di sini!
    }
}
