<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Facades\Storage; 

class CartController extends Controller
{
    /**
     * Affiche le contenu du panier.
     * Route: cart.index
     */
    public function index()
    {
        // Récupère tous les produits du panier
        $cart = Cart::getContent(); 
        // Calcule le total si vous en avez besoin, bien que la vue puisse aussi le faire
        $total = Cart::getTotal(); 
        
        return view('cart.index', compact('cart', 'total'));
    }

    // ------------------------------------------------------------------
    // MÉTHODE STORE - Gère l'ajout avec la variante (ID combiné)
    // ------------------------------------------------------------------
    
    /**
     * Ajoute un produit au panier.
     * Route: cart.store
     */
    public function store(Request $request)
    {
        // ✅ CORRECTION 1 : Le champ est renommé de 'color' à 'variant_name' 
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'variant_name' => 'required|string|max:100', // <-- MIS À JOUR
        ]);

        // Charge le produit ET ses images
        $product = Product::with('images')->findOrFail($request->product_id);
        $quantity = $request->input('quantity', 1);
        
        // ✅ CORRECTION 2 : La variable est récupérée via 'variant_name'
        $chosenVariant = $request->variant_name; 
        
        // 💡 AMÉLIORATION: Cherche l'image principale marquée comme telle
        // NOTE: Il serait encore mieux de chercher l'image correspondant à la variante choisie
        $imageForVariant = $product->images->where('variant_name', $chosenVariant)->first();
        $imageUrl = $imageForVariant ? Storage::url($imageForVariant->path) : (
            $product->images->where('is_main', true)->first() ? Storage::url($product->images->where('is_main', true)->first()->path) : null
        );
        
        // 1. Définir l'ID UNIQUE pour Darryldecode/Cart (Product ID + Variante)
        // Note: L'utilisation de Str::slug est plus robuste que str_replace(' ', '')
        $cartItemId = $product->id . '-' . \Illuminate\Support\Str::slug($chosenVariant); 

        // 2. Vérification rapide de stock (Non géré par la librairie, à faire manuellement)
        if ($product->stock_quantity < $quantity) {
             return back()->withErrors(['quantity' => "Nous n'avons que {$product->stock_quantity} unités de ce produit en stock."]);
        }
        
        // 3. Ajouter l'article au panier
        Cart::add([
            'id' => $cartItemId, 
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $quantity,
            'attributes' => [
                'slug' => $product->slug,
                'image' => $imageUrl,
                'variant' => $chosenVariant, 
            ]
        ]);

        return back()->with('success', "Le produit **{$product->name} ({$chosenVariant})** a été ajouté au panier !");
    }

    // ------------------------------------------------------------------
    // MÉTHODES UPDATE ET DESTROY (Alignées sur le nom de paramètre de la route)
    // ------------------------------------------------------------------

    /**
     * Met à jour la quantité d'un produit dans le panier.
     * Route: cart.update
     */
    public function update(Request $request, $item)
    {
        // ATTENTION : $item est l'ID combiné (ex: '12-Rouge')
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        Cart::update($item, [
            'quantity' => [
                'relative' => false, 
                'value' => $request->quantity
            ]
        ]);

        return back()->with('success', 'Quantité mise à jour !');
    }

    /**
     * Supprime un produit du panier.
     * Route: cart.destroy
     */
    public function destroy($item)
    {
        // ATTENTION : $item est l'ID combiné (ex: '12-Rouge')
        Cart::remove($item);
        return back()->with('success', 'Produit retiré du panier.');
    }

    /**
     * Vide tout le panier.
     * Route: cart.clear
     */
    public function clear()
    {
        Cart::clear();
        return redirect()->route('cart.index')->with('success', '🎉 Le panier a été vidé avec succès !');
    }
}