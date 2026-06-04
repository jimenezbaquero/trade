<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signals', function (Blueprint $table) {
            $table->id();
            
            // mercado
            $table->string('symbol')->index();
            $table->string('timeframe')->nullable();
            
            // señal
            $table->enum('type', ['buy', 'sell', 'hold'])->index();
            $table->decimal('price', 20, 8);
            
            // calidad de la señal
            $table->decimal('confidence', 5, 2)->default(0);
            // ej: 0 - 1 o 0 - 100 según prefieras (yo recomiendo 0-100 si vas a escalar)
            
            // estrategia que la generó
            $table->string('strategy')->index();
            
            // momento exacto del mercado
            $table->timestamp('market_timestamp')->index();
            
            // metadatos flexibles (indicadores usados, razones, etc.)
            $table->json('meta')->nullable();
            
            // estado del ciclo de vida (muy útil a futuro)
            $table->string('status')->default('generated')->index();
            // generated | confirmed | executed | expired | ignored
            
            // timestamps Laravel
            $table->timestamps();
            
            // índices compuestos útiles para trading
            $table->index(['symbol', 'market_timestamp']);
            $table->index(['strategy', 'symbol']);
            
            $table->unique(['symbol', 'timeframe', 'strategy', 'market_timestamp']);
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('signals');
    }
};