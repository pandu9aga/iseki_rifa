<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiPegawai extends Model
{
    use HasFactory;

    protected $table = 'nilai_pegawai';
    protected $fillable = ['employee_id', 'nilai', 'tanggal_penilaian'];

        public $timestamps = false;

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
