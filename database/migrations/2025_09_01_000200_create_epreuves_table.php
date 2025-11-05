<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('epreuves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enseignant_id')->constrained('users')->cascadeOnDelete();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->enum('statut', ['proposee', 'validee', 'rejettee'])->default('proposee');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('epreuves');
    }
};
