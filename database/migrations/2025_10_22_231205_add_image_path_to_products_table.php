<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations (Ajoute la colonne).
     */
    public function up(): void
    {
        // On utilise Schema::table pour MODIFIER la table 'products'
        Schema::table('products', function (Blueprint $table) {
            // AJOUT de la nouvelle colonne pour stocker le chemin de l'image
            // 'string' pour une chaîne de caractères (le chemin)
            // 'nullable()' au cas où l'image ne serait pas obligatoire plus tard
            // 'after('description')' pour la positionner après la colonne description
            $table->string('image_path')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations (Annule la modification).
     */
    public function down(): void
    {
        // On utilise Schema::table pour annuler la modification
        Schema::table('products', function (Blueprint $table) {
            // SUPPRESSION de la colonne image_path
            $table->dropColumn('image_path');
        });
    }
};