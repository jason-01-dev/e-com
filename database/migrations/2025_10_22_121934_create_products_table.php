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
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique();        // Nom unique du produit
        $table->string('slug')->unique();        // URL lisible (pour le SEO)
        $table->text('description');             // Description détaillée
        $table->decimal('price', 8, 2);          // Prix (ex: 99999.99)
        $table->unsignedInteger('stock_quantity');// Quantité en stock
        $table->boolean('is_published')->default(false); // Est-il visible ?
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
