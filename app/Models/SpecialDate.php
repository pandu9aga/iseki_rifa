<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialDate extends Model
{
    use HasFactory;

    protected $table = 'special_dates'; // Nama tabel
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = ['tanggal','jenis_tanggal'];

}
