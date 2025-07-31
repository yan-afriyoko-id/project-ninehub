<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('source')->nullable();
            $table->enum('status', ['Baru', 'Terkualifikasi', 'Tidak Terkualifikasi', 'Konversi'])->default('Baru');
            $table->decimal('potential_value', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
