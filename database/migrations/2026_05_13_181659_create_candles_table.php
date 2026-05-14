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
        Schema::create('candles', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('trading_pair_id')
                ->constrained()
                ->cascadeOnDelete();
            
            $table->string('timeframe', 10)->index();
            // 1m, 5m, 15m, 1h, 4h...
            
            $table->decimal('open', 20, 8);
            $table->decimal('high', 20, 8);
            $table->decimal('low', 20, 8);
            $table->decimal('close', 20, 8);
            
            $table->decimal('volume', 30, 12);
            
            $table->timestamp('opened_at')->index();
            
            $table->timestamps();
            
            $table->unique([
                'trading_pair_id',
                'timeframe',
                'opened_at'
            ], 'candles_unique_idx');
            
            $table->index([
                'trading_pair_id',
                'timeframe',
                'opened_at'
            ], 'candles_lookup_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candles');
    }
};
