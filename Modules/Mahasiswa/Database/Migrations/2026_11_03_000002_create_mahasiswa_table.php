<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_prodi')->constrained('prodi')->restrictOnDelete();
            $table->string('nama', 100);
            $table->string('nim', 30)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
