<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Employee extends Model
{
    use HasFactory; //, LogsActivity;

    protected $fillable = ['nama', 'nik', 'team', 'division_id', 'status'];

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //         ->useLogName('employee')
    //         ->logOnly(['nama', 'nik', 'status', 'division_id', 'team'])
    //         ->logOnlyDirty()
    //         ->setDescriptionForEvent(function (string $eventName) {
    //             $causer = Auth::check() ? Auth::user()->name : 'Guest';
    //             return "{$causer} melakukan {$eventName} data karyawan {$this->nama}";
    //         });
    // }
}
