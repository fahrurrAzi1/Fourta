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
        Schema::create('skor_jawaban', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_siswa')->nullable(); 
            $table->unsignedBigInteger('id_soal');
            $table->unsignedBigInteger('kelas_id')->nullable();
            $table->enum('jenis', ['literasi', 'numerasi']);
            $table->integer('skor_jawaban_siswa')->notNullable();
            $table->integer('skor_yakin_jawaban')->notNullable();
            $table->integer('skor_alasan')->notNullable();
            $table->integer('skor_yakin_alasan')->notNullable();
            $table->integer('skor_akhir'); 
            $table->string('kategori_skor'); 
            $table->unsignedBigInteger('id_komentar')->nullable();
            $table->timestamps();

            $table->foreign('id_siswa')->references('id_siswa')->on('siswas')->onDelete('cascade');
            $table->foreign('id_soal')->references('id')->on('soal')->onDelete('cascade');
            $table->foreign('id_komentar')->references('id')->on('komentar')->onDelete('cascade');
            $table->foreign('kelas_id')->references('id')->on('kelass')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skor_jawaban');
    }
};
