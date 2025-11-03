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
        Schema::table('product_images', function (Blueprint $table) {
            // 🛑 AJOUT DE LA COLONNE 'variant_name'
            // 'string' est le type, 100 est la longueur max, 'nullable' permet qu'elle soit vide.
            $table->string('variant_name', 100)->nullable()->after('path');
        });
    }

    /**
     * Reverse the migrations (Supprime la colonne).
     */
    public function down(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            // 🛑 SUPPRESSION DE LA COLONNE
            $table->dropColumn('variant_name');
        });
    }
};