<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->string('judul');
            $table->text('isi');
            $table->enum('status_main', ['belum', 'sedang', 'tamat', 'dropped'])->default('belum');
            $table->unsignedInteger('jam_main')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'game_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
