<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Order;    
use App\Models\OrderItem; 
// Si vous utilisez l'authentification : use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    /**
     * Affiche le formulaire de caisse (Checkout)
     * Route: checkout.index
     */
    public function index()
    {
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide. Impossible de procéder à la caisse.');
        }

        $totalGlobal = 0;
        foreach ($cart as $item) {
            $totalGlobal += $item['price'] * $item['quantity'];
        }

        $customerData = [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
        ];
        
        return view('checkout.index', [
            'cart' => $cart,
            'totalGlobal' => $totalGlobal,
            'customerData' => $customerData,
        ]);
    }

    /**
     * Traite la soumission du formulaire et ENREGISTRE la commande dans la BDD.
     * Route: checkout.store
     */
    public function store(Request $request)
    {
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Panier vide. Commande non traitée.');
        }

        // 1. Validation des données du formulaire
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'zip_code' => 'required|string|max:10',
            'country' => 'required|string|max:255',
        ]);
        
        // 2. Calcul du Total Final
        $totalAmount = 0;
        foreach ($cart as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        // 3. ENREGISTREMENT DANS LA BASE DE DONNÉES
        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => null, // À remplacer par Auth::id() si connecté
                'customer_first_name' => $validatedData['first_name'],
                'customer_last_name' => $validatedData['last_name'],
                'customer_email' => $validatedData['email'],
                'shipping_address' => $validatedData['address'],
                'shipping_city' => $validatedData['city'],
                'shipping_zip_code' => $validatedData['zip_code'],
                'shipping_country' => $validatedData['country'],
                'total_amount' => $totalAmount,
                'status' => 'pending', 
            ]);

            foreach ($cart as $productId => $details) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'product_name' => $details['name'],
                    'quantity' => $details['quantity'],
                    'price_at_sale' => $details['price'],
                ]);
            }

            DB::commit();

            // 4. Vider le panier
            Session::forget('cart');

            // 5. Redirection vers la confirmation
            return redirect()->route('checkout.confirmation', ['order' => $order->id])->with('success', 
                'Votre commande a été passée avec succès !'
            );

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', 
                'Erreur critique lors de l\'enregistrement de la commande.'
            );
        }
    }

    /**
     * Affiche la page de confirmation de la commande.
     * Route: checkout.confirmation
     */
    public function confirmation(Order $order)
    {
        // Charge les lignes de commande (items) pour l'affichage du récapitulatif
        $order->load('items'); 

        return view('checkout.confirmation', [
            'order' => $order,
        ]);
    }
}