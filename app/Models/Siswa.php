<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Siswa extends Authenticatable
{
    use HasFactory, Notifiable;

    // protected $siswa = 'siswas';
    // protected $table = 'siswas';
    
    protected $guard = 'siswa';

    protected $primaryKey = 'id_siswa';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'nis',
        'email',
        'password',
        'id_kelas',
        'nama_sekolah'
    ];

    public function kelass()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }
    

    public function hasil()
    {
        return $this->hasMany(Hasil::class, 'id_siswa');
    }

    /** 
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}