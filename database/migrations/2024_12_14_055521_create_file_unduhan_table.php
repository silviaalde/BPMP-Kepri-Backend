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
        Schema::create('file_unduhan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('unduhan_id');
            $table->string('title', 255);
            $table->string('file');
            $table->string('size', 50);
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('unduhan_id')->references('id')->on('unduhan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_unduhan');
    }
};
