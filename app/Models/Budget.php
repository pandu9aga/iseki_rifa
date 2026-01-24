<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $table = 'budgets';
    protected $primaryKey = 'Id_Budget'; // sesuai dengan DB
    public $timestamps = false; // karena tidak ada created_at/updated_at

    protected $fillable = [
        'Tanggal_Budget',
        'Jumlah_Budget'
    ];

    // Opsional: agar tetap bisa akses via $budget->bulan_tahun di view
    public function getBulanTahunAttribute()
    {
        return $this->attributes['Tanggal_Budget'];
    }

    public function getJumlahBudgetAttribute()
    {
        return $this->attributes['Jumlah_Budget'];
    }
}
