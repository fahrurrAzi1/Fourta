<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkorJawaban extends Model
{
    use HasFactory;

    protected $table = 'skor_jawaban';

    protected $fillable = [
        'id_siswa',
        'kelas_id',
        'id_soal',
        'jenis',
        'skor_jawaban_siswa',
        'skor_yakin_jawaban',
        'skor_alasan',
        'skor_yakin_alasan',
        'id_komentar',
        'skor_akhir',
        'kategori_skor',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function soal()
    {
        return $this->belongsTo(Soal::class, 'id_soal');
    }

    public function komentar()
    {
        return $this->belongsTo(Komentar::class, 'id_komentar');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

}