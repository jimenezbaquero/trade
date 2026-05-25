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

            $table->foreignId('pair_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('timeframe', 10)->index();
            // 1m, 5m, 15m, 1h, 4h...

            // TIEMPO
            $table->timestamp('opened_at')->index();
            $table->timestamp('closed_at')->index()->nullable();
            
            $table->boolean('is_closed')->default(true)->index();

            $table->decimal('open', 20, 8);
            $table->decimal('high', 20, 8);
            $table->decimal('low', 20, 8);
            $table->decimal('close', 20, 8);

            $table->decimal('volume', 30, 12);
            $table->decimal('quote_volume', 30, 12)->nullable();
            
            $table->unsignedInteger('trades_count')->nullable()->index();
            
            $table->decimal('taker_buy_base_volume', 30, 12)->nullable();
            $table->decimal('taker_buy_quote_volume', 30, 12)->nullable();

            $table->timestamps();

            $table->unique([
                'pair_id',
                'timeframe',
                'opened_at'
            ], 'candles_unique_idx');

            $table->index([
                'pair_id',
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
