<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hasil extends Model
{
    use HasFactory;

    protected $table = 'hasil';

    protected $fillable = [
        'id_soal',
        'id_siswa',
        'jawaban_siswa',
        'status_jawaban',
        'alasan_siswa',
        'status_alasan',
        'kelas_id',
    ];

    public function soal()
    {
        return $this->belongsTo(Soal::class, 'id_soal');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function kelass()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}