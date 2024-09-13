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
        Schema::create('hasil', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_siswa')->nullable(); 
            $table->unsignedBigInteger('id_soal');
            $table->unsignedBigInteger('kelas_id')->nullable();
            $table->text('jawaban_siswa');
            $table->enum('status_jawaban',['yakin','tidak']);
            $table->text('alasan_siswa');
            $table->enum('status_alasan',['yakin','tidak']);
            $table->timestamps();

            $table->foreign('id_siswa')->references('id_siswa')->on('siswas')->onDelete('cascade');

            $table->foreign('id_soal')->references('id')->on('soal')->onDelete('cascade');

            $table->foreign('kelas_id')->references('id')->on('kelass')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil', function (Blueprint $table) {
            $table->dropForeign(['id_siswa']);
            $table->dropForeign(['id_soal']);
        });

        Schema::dropIfExists('hasil');
    }
};
