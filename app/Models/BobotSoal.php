<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BobotSoal extends Model
{
    use HasFactory;

    protected $table = 'bobot_soal';

    protected $fillable = [
        'id_soal',
        'skor_jawaban_siswa',
        'skor_yakin_jawaban',
        'skor_alasan',
        'skor_yakin_alasan',
    ];

    public function soal()
    {
        return $this->belongsTo(Soal::class, 'id_soal');
    }
}