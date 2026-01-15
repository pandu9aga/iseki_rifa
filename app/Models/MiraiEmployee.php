<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MiraiEmployee extends Model
{
    protected $connection = 'mirai'; // Koneksi database Mirai
    use HasFactory;

    protected $table = 'employees';

    protected $fillable = [
        'id',
        'department_id',
        'department_other',
        'employee_number',
        'employee_name',
        'employee_status',
        'employee_type',
        'employee_date',
        'employee_category'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    /**
     * Relasi ke LeaveBalance (1 karyawan bisa punya banyak leave balance per tahun)
     */
    public function leaveBalances()
    {
        return $this->hasMany(MiraiLeaveBalance::class, 'employee_id', 'id');
    }

    /**
     * Relasi ke LeaveBalance untuk tahun tertentu (digunakan dengan eager loading constraint)
     */
    public function leaveBalance()
    {
        return $this->hasOne(MiraiLeaveBalance::class, 'employee_id', 'id');
    }

    /**
     * Get leave balance untuk tahun berjalan
     */
    public function currentLeaveBalance()
    {
        return $this->hasOne(MiraiLeaveBalance::class, 'employee_id', 'id')
            ->where('year', now()->year);
    }
}