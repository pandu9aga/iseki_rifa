<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Overtime extends Model
{
    protected $connection = 'mirai';

    protected $fillable = [
        'karyawan_id',
        'user_id',
        'request_date',
        'request_time',
        'actual_date',
        'actual_time',
        'break',
        'remark',
        'is_need_to_eat',
        'is_mark_incomplete',
        'is_mark_cancel',
        'status_before_cancel',
        'status',
        'kode',
        'id_rifa',
    ];

    protected $casts = [
        'is_need_to_eat' => 'boolean',
        'is_mark_incomplete' => 'boolean',
        'is_mark_cancel' => 'boolean',
        'request_date' => 'date',
        'actual_date' => 'date',
    ];

    // Relasi
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'karyawan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ✅ Fungsi Generate Kode Overtime Otomatis
    public static function generateOvertimeCode()
    {
        $prefix = 'OT-'; // Anda bisa ubah jadi 'REQ-' jika mau konsisten, tapi disarankan bedakan
        $date   = now()->format('ymd'); // contoh: 251117

        // Hitung jumlah overtime yang dibuat hari ini
        $countToday = self::whereDate('created_at', today())->count() + 1;

        // Padding 4 digit
        $urut = str_pad($countToday, 4, '0', STR_PAD_LEFT);

        return $prefix . $date . $urut;
    }
}
