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
        Schema::create('trading_pairs', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('exchange_id')
                ->constrained()
                ->cascadeOnDelete();
            
            $table->string('symbol')->index();
            
            $table->string('base_asset', 20);
            $table->string('quote_asset', 20);
            
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            $table->unique(['exchange_id', 'symbol']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trading_pairs');
    }
};
