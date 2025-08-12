<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Replacement extends Model
{
    use HasFactory;

    protected $fillable = ['absensi_id', 'replacer_nik', 'production_number', 'created_at'];

    public $timestamps = false;

    public function absensi()
    {
        return $this->belongsTo(Absensi::class);
    }
    
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'replacer_nik', 'nik');
    }
}
