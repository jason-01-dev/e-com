<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Supprime la colonne "image_path"
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }

    public function down(): void
    {
        // Re-crée la colonne au cas où on voudrait annuler la suppression
        Schema::table('products', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('description');
        });
    }
};