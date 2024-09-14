<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id('id_siswa');
            $table->string('name');
            $table->string('nis')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('siswa');
            $table->string('id_kelas')->nullable();
            $table->string('nama_sekolah')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
        Schema::dropIfExists('id_kelas');
        Schema::dropIfExists('sekolah');
    }
};
