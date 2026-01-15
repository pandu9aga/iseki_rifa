<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MiraiLeaveBalance extends Model
{
    protected $connection = 'mirai'; // Koneksi database Mirai
    use HasFactory;

    protected $table = 'leave_balances';

    protected $fillable = [
        'employee_id',
        'year',
        'base_leave',
        'prorated',
        'carry_forward',
        'additional_leave_10yrs',
        'company_leave',
        'saturday_work',
        'deducted_leave',
        'remaining_leave',
        'status', // enum: DRAFT, FINAL
        'calculated_at',
    ];

    protected $casts = [
        'prorated' => 'boolean',
        'year' => 'integer',
        'deducted_leave' => 'float',
        'remaining_leave' => 'float',
        'calculated_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(MiraiEmployee::class);
    }
}