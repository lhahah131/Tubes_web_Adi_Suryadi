<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanAbsensi extends Model
{
    use HasFactory;

    protected $table = 'laporan_absensi';
    protected $fillable = ['kelas_id', 'bulan', 'tahun', 'total_hari_efektif'];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function detailLaporan()
    {
        return $this->hasMany(DetailLaporanAbsensi::class);
    }
}
