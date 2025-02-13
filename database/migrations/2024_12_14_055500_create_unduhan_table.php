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
        Schema::create('unduhan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 255);
            $table->longText('content');
            $table->enum('category', ['Sakip', 'SPI', 'POS', 'RBI', 'Layanan Informasi Publik']);
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unduhan');
    }
};
