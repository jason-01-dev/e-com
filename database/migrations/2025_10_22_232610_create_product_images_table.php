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
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            
            // 🚀 AJOUTS ESSENTIELS 🚀
            $table->string('path'); // Le chemin du fichier image
            $table->boolean('is_main')->default(false); // Est-ce l'image principale ?
            
            // Clé étrangère vers la table 'products', avec suppression en cascade
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};