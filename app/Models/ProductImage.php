<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'path',
        'is_main',
        'variant_name', // ⬅️ AJOUTÉ : Permet d'enregistrer le nom de la variante
    ];

    // Relation inverse : une image appartient à un seul produit
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}