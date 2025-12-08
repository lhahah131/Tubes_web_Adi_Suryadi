<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'guru';
    protected $fillable = ['user_id', 'nip', 'mata_pelajaran', 'tanggal_lahir', 'alamat', 'nomor_telepon'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class);
    }
}
