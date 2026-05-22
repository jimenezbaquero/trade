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
        Schema::create('pairs', function (Blueprint $table) {
            
            $table->id();
            
            $table->foreignId('exchange_id')
                ->constrained()
                ->cascadeOnDelete();
            
            // BTC
            $table->string('base_asset', 20);
            
            // USDT
            $table->string('quote_asset', 20);
            
            // BTCUSDT
            $table->string('symbol');
            
            // trading status
            $table->string('status')->nullable();
            
            // precision
            $table->unsignedInteger('price_precision')->nullable();
            $table->unsignedInteger('quantity_precision')->nullable();
            
            // filters
            $table->decimal('min_qty', 30, 16)->nullable();
            $table->decimal('max_qty', 30, 16)->nullable();
            
            $table->decimal('tick_size', 30, 16)->nullable();
            $table->decimal('step_size', 30, 16)->nullable();
            
            // volume info
            $table->decimal('min_notional', 30, 16)->nullable();
            
            // metadata from exchange
            $table->json('metadata')->nullable();
            
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // indexes
            $table->index('symbol');
            $table->index('base_asset');
            $table->index('quote_asset');
            $table->index('status');
            
            $table->unique([
                'exchange_id',
                'symbol'
            ]);
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pairs');
    }
};