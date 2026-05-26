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
        Schema::create('indicators', function (Blueprint $table) {
            $table->id();
            
            // Identificador lógico (ema, rsi, macd...)
            $table->string('code')->index();
            
            // Nombre legible
            $table->string('name');
            
            // Descripción humana (UI / docs / debug)
            $table->text('description')->nullable();
            
            /**
             * Handler que ejecuta el cálculo
             * Ej:
             * - ema
             * - rsi
             * - macd
             *
             * o incluso clase:
             * App\Domain\Indicators\EmaIndicator
             */
            $table->string('handler')->index();
            
            /**
             * Configuración del indicador en JSON
             * Ej:
             * { "period": 50, "source": "close" }
             */
            $table->json('config');
            
            $table->timestamps();
            
            // Evita duplicados de configs idénticas por código
            $table->unique(['code', 'handler']);
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicators');
    }
};
