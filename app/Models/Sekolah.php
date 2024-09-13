<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    use HasFactory;

    protected $table = 'sekolah';

    protected $fillable = [ 
        'kelas_id',
        'nama_sekolah',
    ];

    
    public function kelass()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
    
}
