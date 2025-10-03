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
        Schema::create('revenues', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->foreignId('fonte_id')->constrained('categories');
            $table->foreignId('bloco_id')->constrained('categories');
            $table->foreignId('grupo_id')->nullable()->constrained('categories');
            $table->foreignId('acao_id')->nullable()->constrained('categories');
            $table->text('observation')->nullable();
            $table->timestamps();

            // Índices para melhor performance
            $table->index('date');
            $table->index(['fonte_id', 'bloco_id', 'grupo_id', 'acao_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenues');
    }
};
