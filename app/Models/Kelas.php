<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    protected $fillable = ['nama_kelas', 'wali_kelas'];

    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }

    public function guru()
    {
        return $this->belongsToMany(Guru::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    public function qrCodes()
    {
        return $this->hasMany(QrCode::class);
    }

    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    public function laporanAbsensi()
    {
        return $this->hasMany(LaporanAbsensi::class);
    }
}
