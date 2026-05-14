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
        Schema::create('exchanges', function (Blueprint $table) {
            $table->id();
            
            $table->string('name');
            $table->string('slug')->unique();
            
            $table->string('api_url')->nullable();
            $table->string('testnet_api_url')->nullable();
            
            $table->string('websocket_url')->nullable();
            $table->string('testnet_websocket_url')->nullable();
            
            $table->unsignedInteger('rate_limit')->nullable();
            
            $table->json('metadata')->nullable();
            
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchanges');
    }
};
