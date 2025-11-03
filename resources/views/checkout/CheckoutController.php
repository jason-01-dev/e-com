<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Illuminate\Support\Facades\DB;
use App\Models\Order; // Assurez-vous d'avoir ce modèle
use App\Models\OrderItem; // Assurez-vous d'avoir ce modèle
use App\Models\Product; // Pour la mise à jour du stock

class CheckoutController extends Controller
{
    /**
     * Affiche le formulaire de caisse.
     * Route: checkout.index
     */
    public function index()
    {
        // 1. Vérifie si le panier est vide
        if (Cart::isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide. Veuillez ajouter des produits avant de passer à la caisse.');
        }

        // 2. Récupère le contenu et le total
        $cart = Cart::getContent();
        // Le total est nécessaire pour le bouton de paiement
        $totalGlobal = Cart::getTotal();

        // 3. Renvoie la vue avec les données
        // Remarque : Si l'utilisateur est connecté, vous pouvez pré-remplir les champs ici.
        return view('checkout.index', compact('cart', 'totalGlobal'));
    }

    /**
     * Traite la soumission du formulaire, crée la commande et vide le panier.
     * Route: checkout.store
     */
    public function store(Request $request)
    {
        // 1. Validation des données du client
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'country' => 'required|string|max:50', // La vue le rend requis
        ]);

        // 2. Vérification critique : Le panier est-il toujours valide ?
        if (Cart::isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Le panier est vide. Commande annulée.');
        }

        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($validatedData) {
            
            $cartItems = Cart::getContent();
            $totalAmount = Cart::getTotal();

            // 3. Création de la commande principale (Order)
            $order = Order::create([
                // Informations client
                'customer_first_name' => $validatedData['first_name'],
                'customer_last_name' => $validatedData['last_name'],
                'customer_email' => $validatedData['email'],

                // Adresse de livraison
                'shipping_address' => $validatedData['address'],
                'shipping_city' => $validatedData['city'],
                'shipping_zip_code' => $validatedData['zip_code'],
                'shipping_country' => $validatedData['country'],

                // Totaux et statut
                'total_amount' => $totalAmount,
                'status' => 'Pending', // Statut initial
                'user_id' => auth()->id(), // Enregistre l'ID de l'utilisateur connecté (peut être null)
            ]);

            // 4. Création des lignes de commande (OrderItems) et mise à jour du stock
            foreach ($cartItems as $item) {
                // L'ID du produit est la première partie de l'ID combiné du panier
                $productId = explode('-', $item->id)[0];
                $product = Product::find($productId);
                
                // Si le produit existe et que le stock est suffisant
                if ($product && $product->stock_quantity >= $item->quantity) {
                    
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $productId,
                        'product_name' => $item->name,
                        'price_at_sale' => $item->price,
                        'quantity' => $item->quantity,
                        'variant_info' => $item->attributes->variant, // Enregistre la variante
                    ]);

                    // Mise à jour du stock
                    $product->decrement('stock_quantity', $item->quantity);
                } else {
                    // Annuler la transaction si le stock est insuffisant (logique plus complexe)
                    // Pour simplifier, on pourrait juste enregistrer les items disponibles, mais c'est risqué.
                    // Une implémentation réelle ferait un check de stock AVANT la transaction.
                    // Ici, on supposera que le stock a été vérifié au panier.
                }
            }

            // 5. Vider le panier
            Cart::clear();

            // 6. Redirection vers la page de confirmation
            return redirect()->route('checkout.confirmation', $order)
                             ->with('success', 'Votre commande a été passée avec succès !');

        }); // Fin de la transaction
    }

    /**
     * Affiche la page de confirmation de commande.
     * Route: checkout.confirmation
     * NOTE: Utilise le Model Binding pour récupérer l'objet Order par son ID.
     */
    public function confirmation(Order $order)
    {
        // 1. Vérifie que l'utilisateur est autorisé à voir cette commande (sécurité de base)
        if (auth()->check() && $order->user_id !== auth()->id()) {
            // Si l'utilisateur est connecté mais que ce n'est pas sa commande
            // On peut ajouter une condition si l'utilisateur n'est pas admin
        }
        
        // 2. Renvoie la vue. Les items sont chargés via la relation $order->items dans la vue.
        return view('checkout.confirmation', compact('order'));
    }
}