<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailLaporanAbsensi extends Model
{
    use HasFactory;

    protected $table = 'detail_laporan_absensi';
    protected $fillable = ['laporan_absensi_id', 'siswa_id', 'hadir', 'absen', 'sakit', 'izin', 'persentase_kehadiran'];

    public function laporanAbsensi()
    {
        return $this->belongsTo(LaporanAbsensi::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
