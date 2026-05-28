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
        Schema::create('indicator_values', function (Blueprint $table) {
            
            $table->id();
            
            $table->foreignId('indicator_id')
                ->constrained()
                ->cascadeOnDelete();
            
            $table->foreignId('candle_id')
                ->constrained()
                ->cascadeOnDelete();
            
            $table->json('value');
            
            $table->timestamps();
            
            $table->unique([
                'indicator_id',
                'candle_id'
            ]);
            
            $table->index('candle_id');
            
            $table->index('indicator_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicator_values');
    }
};
