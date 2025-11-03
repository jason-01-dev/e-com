<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];
    
    // Ajout du Route Key Name pour utiliser le SLUG dans les routes
    public function getRouteKeyName()
    {
        return 'slug';
    }
    
    // Relation : Une catégorie a plusieurs produits
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}