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
        Schema::create('simulated_signals', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('simulation_id')
                ->constrained()
                ->cascadeOnDelete();
            
            // señal
            $table->enum('type', ['buy', 'sell', 'hold']);
            $table->decimal('price', 20, 8);
            
            // confianza
            $table->decimal('confidence', 5, 2)->default(0);
            
            // estrategia
            $table->string('strategy');
            
            // momento de mercado
            $table->timestamp('market_timestamp');
            
            // referencia a la vela
            $table->foreignId('candle_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            
            // información adicional
            $table->json('meta')->nullable();
            
            $table->timestamps();
            
            $table->index(['simulation_id']);
            $table->index(['market_timestamp']);
            $table->index(['strategy']);
            
            $table->unique([
                'simulation_id',
                'strategy',
                'market_timestamp'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulated_signals');
    }
};
