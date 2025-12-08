<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanIzin extends Model
{
    protected $table = 'pengajuan_izin';

    protected $fillable = [
        'user_id',
        'jenis',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'file_bukti',
        'status',
        'catatan_guru',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'approved_at' => 'datetime',
    ];

    // Relationship dengan User (siswa yang mengajukan)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship dengan User (guru yang approve)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
