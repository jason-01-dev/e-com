<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Inclut 'category_id' suite à la migration.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 
        'slug', 
        'description', 
        'price', 
        'stock_quantity', 
        'is_published',
        'category_id', // 💡 AJOUTÉ pour lier à la catégorie
    ];

    /**
     * Get the route key for the model.
     * Utilise le SLUG au lieu de l'ID pour les URLs propres.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    // 📸 Relation : Un produit a plusieurs images
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
    
    // 🏷️ Relation : Un produit appartient à une catégorie
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}