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
        Schema::create('simulated_decisions', function (Blueprint $table) {
            $table->id();
            
            // contexto de mercado
            $table->foreignId('simulated_signal_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            
            // decisión tomada por el engine
            $table->string('action')->index();
            // open_long | open_short | close | ignore
            
            // precio de referencia
            $table->decimal('price', 20, 8)->nullable();
            
            // confianza / score del decision engine (si aplica)
            $table->decimal('confidence', 5, 2)->default(0);
            
            // origen
            $table->string('strategy')->nullable()->index();
            
            // timestamp del mercado (no el de ejecución)
            $table->timestamp('market_timestamp')->index();
            
            // modo de ejecución
            $table->string('mode')->index();
            // live | simulation
            
            // metadatos (razones, indicadores, etc.)
            $table->json('meta')->nullable();
            
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulated_decisions');
    }
};
