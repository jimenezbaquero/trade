<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simulations', function (Blueprint $table) {
            $table->id();
            
            // identificación
            $table->string('name');
            $table->string('strategy');
            
            // mercado analizado
            $table->foreignId('pair_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('timeframe');
            
            // rango temporal
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            
            // configuración
            $table->json('settings')->nullable();
            
            $table->timestamps();
            
            $table->index(['strategy']);
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('simulations');
    }
};