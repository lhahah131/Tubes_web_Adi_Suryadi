<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';
    protected $fillable = ['user_id', 'nis', 'nomor_induk', 'kelas_id', 'tanggal_lahir', 'alamat', 'nomor_telepon'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    public function detailLaporanAbsensi()
    {
        return $this->hasMany(DetailLaporanAbsensi::class);
    }
}
