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
        Schema::create('bobot_soal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_soal');
            $table->enum('skor_jawaban_siswa',[0,1])->notNullable();
            $table->enum('skor_yakin_jawaban',[0,1])->notNullable();
            $table->enum('skor_alasan',[0,1])->notNullable();
            $table->enum('skor_yakin_alasan',[0,1])->notNullable();
            $table->timestamps();

            $table->foreign('id_soal')->references('id')->on('soal')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bobot_soal');
    }
};
