<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelass';

    protected $fillable = [
        'id_kelas', 
        'nama_sekolah', 
        'guru_id',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'id_kelas', 'id');
    }

    public function siswadankelas()
    {
        return $this->hasMany(Siswa::class, 'id_kelas', 'id');
    }
    
    public function soal()
    {
        return $this->hasMany(Soal::class, 'kelas_id');
    }

    public function hasil()
    {
        return $this->hasMany(Hasil::class, 'kelas_id');
    }

    public function skor_jawaban()
    {
        return $this->hasMany(SkorJawaban::class, 'kelas_id');
    }

    public function komentar()
    {
        return $this->hasMany(Komentar::class, 'kelas_id');
    }
    
}
