<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // 💡 NOUVEAU

class Order extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être massivement assignés (Mass Assignable).
     */
    protected $fillable = [
        'user_id',
        'customer_first_name',
        'customer_last_name',
        'customer_email',
        
        'shipping_address',
        'shipping_city',
        'shipping_zip_code',
        'shipping_country',
        
        'total_amount',
        'status',
    ];

    /**
     * Une commande a plusieurs lignes de commande (Order Items).
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class); 
    }
    
    /**
     * 💡 NOUVEAU : Une commande appartient à un utilisateur (si l'utilisateur était connecté).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class); 
    }
}