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
        Schema::create('kelass', function (Blueprint $table) {
            $table->id();
            $table->string('id_kelas')->nullable();
            $table->string('nama_sekolah')->nullable();
            $table->unsignedBigInteger('guru_id');
            $table->timestamps();

            $table->foreign('guru_id')->references('id_guru')->on('gurus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelass');
    }
};
