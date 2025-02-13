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
        Schema::create('penghargaan', function (Blueprint $table) {
            $table->uuid('id')->primary();  // Menggunakan UUID sebagai primary key
            $table->string('image');
            $table->string('title');
            $table->longText('content');
            $table->string('location');
            $table->date('date');
            $table->enum('category', ['BPMP Prov.kepri', 'Pemerintahan Daerah', 'Satuan Pendidikan']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penghargaan');
    }
};
