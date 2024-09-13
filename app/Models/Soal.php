<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    use HasFactory;

    protected $table = 'soal';

    protected $fillable = [
        'jenis',
        'pertanyaan',
        'status',
        'kelas_id',
        'waktu',
    ];

    public function hasil()
    {
        return $this->hasMany(Hasil::class, 'id_soal');
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