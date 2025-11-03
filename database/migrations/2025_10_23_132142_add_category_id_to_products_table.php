<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Ajout de la clé étrangère
            $table->foreignId('category_id')
                  ->nullable() // Permet d'avoir des produits sans catégorie au début
                  ->constrained()
                  ->onDelete('set null'); // Si une catégorie est supprimée, le category_id du produit devient NULL
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};