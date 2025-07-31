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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama plan (Free, Basic, Premium, Enterprise)
            $table->string('slug')->unique(); // Slug untuk identifikasi (free, basic, premium, enterprise)
            $table->text('description')->nullable(); // Deskripsi plan
            $table->decimal('price', 10, 2)->default(0); // Harga plan
            $table->string('currency', 3)->default('IDR'); // Mata uang
            $table->integer('max_users')->default(5); // Maksimal user
            $table->integer('max_storage')->default(100); // Maksimal storage dalam MB
            $table->json('features')->nullable(); // Fitur yang tersedia
            $table->boolean('is_active')->default(true); // Status aktif plan
            $table->timestamps();

            $table->index(['is_active', 'price']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
