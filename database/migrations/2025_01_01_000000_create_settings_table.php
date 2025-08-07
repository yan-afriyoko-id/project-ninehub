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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->string('type', 50)->default('string');
            $table->string('group', 100)->default('general');
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['group', 'is_public']);
            $table->index(['user_id', 'is_public']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
