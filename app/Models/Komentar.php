<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komentar extends Model
{
    use HasFactory;
    
    protected $table = 'komentar';

    protected $fillable = [
        'kelas_id',
        'id_siswa',
        'id_soal',
        'isi_komentar',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function soal()
    {
        return $this->belongsTo(Soal::class, 'id_soal');
    }

    public function kelass()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
    
}
