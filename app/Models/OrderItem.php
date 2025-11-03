<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;
    
    // Le nom de table par défaut est 'order_items'

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'quantity',
        'price_at_sale',
        'variant_info', // 💡 AJOUT CRUCIAL : Pour stocker "Rouge", "Taille L", etc.
    ];

    /**
     * Une ligne de commande appartient à une commande (Order).
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    
    /**
     * 💡 AJOUT : Relation inverse vers le produit original.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}