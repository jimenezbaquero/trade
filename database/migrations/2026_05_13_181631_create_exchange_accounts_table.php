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
        Schema::create('exchange_accounts', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            
            $table->foreignId('exchange_id')
                ->constrained()
                ->cascadeOnDelete();
            
            $table->string('name');
            
            $table->text('api_key');
            $table->text('api_secret');
            
            $table->boolean('is_testnet')->default(true);
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_accounts');
    }
};
