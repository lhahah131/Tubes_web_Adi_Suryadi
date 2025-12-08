<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    use HasFactory;

    protected $table = 'qr_codes';
    protected $fillable = ['kelas_id', 'kode_qr', 'waktu_dibuat', 'waktu_berlaku_sampai', 'aktif'];
    protected $casts = [
        'waktu_dibuat' => 'datetime',
        'waktu_berlaku_sampai' => 'datetime',
        'aktif' => 'boolean',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }
}
