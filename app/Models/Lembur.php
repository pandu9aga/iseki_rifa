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
        'approval_leader',
    ];

    protected $primaryKey = 'id_lembur';
    
    public $timestamps = false; // 


    protected $casts = [
        'tanggal_lembur' => 'date',
        'approval_lembur' => 'boolean',
        'approval_leader' => 'boolean',
        'durasi_lembur' => 'float',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Optional: label persetujuan super
    public function getApprovalLabelAttribute()
    {
        return match ($this->approval_lembur) {
            true => 'Disetujui',
            false => 'Ditolak',
            default => 'Menunggu',
        };
    }

    // Optional: label persetujuan leader
    public function getApprovalLeaderLabelAttribute()
    {
        return match ($this->approval_leader) {
            true => 'Disetujui',
            false => 'Ditolak',
            default => 'Menunggu',
        };
    }
}
