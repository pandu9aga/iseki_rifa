<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lembur extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'divisi',
        'tanggal_lembur',
        'waktu_lembur',
        'durasi_lembur',
        'keterangan_lembur',
        'makan_lembur',
        'approval_lembur',
    ];

    protected $primaryKey = 'id_lembur';
    
    public $timestamps = false; // 


    protected $casts = [
        'tanggal_lembur' => 'date',
        'approval_lembur' => 'boolean',
        'durasi_lembuar' => 'float',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Optional: label persetujuan
    public function getApprovalLabelAttribute()
    {
        return match ($this->approval_lembur) {
            true => 'Disetujui',
            false => 'Ditolak',
            default => 'Menunggu',
        };
    }
}
