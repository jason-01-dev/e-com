<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FrontController; 
use App\Http\Controllers\ProductController; 
use App\Http\Controllers\ProductImageController; 
use App\Http\Controllers\CategoryController; 
use App\Http\Controllers\ProfileController; // ← ajouté

// ----------------------------------------------------------------------
// --- Routes du Front-End (Catalogue & Détail) ---
// ----------------------------------------------------------------------

// Route d'accueil (Catalogue sans filtre)
Route::get('/', [FrontController::class, 'index'])->name('front.index');

// Route pour le catalogue filtré par catégorie (Model Binding sur le slug)
Route::get('/category/{category:slug}', [FrontController::class, 'category'])->name('front.category');

// Affichage du produit (Model Binding sur le slug)
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('front.product.show');

// ----------------------------------------------------------------------
// --- Routes pour le Panier (Cart) ---
// ----------------------------------------------------------------------
Route::controller(CartController::class)->group(function () {
    
    // Affichage du panier
    Route::get('/cart', 'index')->name('cart.index'); 
    
    // Ajout au panier
    Route::post('/cart/add', 'store')->name('cart.store'); 
    
    // Mise à jour de la quantité
    Route::patch('/cart/{item}', 'update')->name('cart.update');
    
    // Suppression d'un article
    Route::delete('/cart/{item}', 'destroy')->name('cart.destroy');
    
    // Vider le panier
    Route::post('/cart/clear', 'clear')->name('cart.clear'); 
});

// ----------------------------------------------------------------------
// --- Routes pour la Caisse (Checkout) ---
// ----------------------------------------------------------------------
Route::controller(CheckoutController::class)->group(function () {
    Route::get('/checkout', 'index')->name('checkout.index');
    Route::post('/checkout', 'store')->name('checkout.store');
    Route::get('/checkout/confirmation/{order}', 'confirmation')->name('checkout.confirmation');
});

// ----------------------------------------------------------------------
// --- Routes d'Administration (Protégées par auth et admin middleware) ---
// ----------------------------------------------------------------------
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // 1. Routes pour les Produits (CRUD complet)
    Route::resource('products', ProductController::class);
    
    // 2. Routes pour les Catégories (CRUD sans 'show')
    Route::resource('categories', CategoryController::class)->except(['show']); 
    
    // 3. Routes pour les Images de Produits (Actions spécifiques)
    Route::put('product-images/{image}/set-main', [ProductImageController::class, 'setMainImage'])
        ->name('product_images.set_main');
        
    Route::delete('product-images/{image}', [ProductImageController::class, 'destroy'])
        ->name('product_images.destroy');
});

// ----------------------------------------------------------------------
// --- Route Dashboard pour éviter l'erreur post-registration ---
// ----------------------------------------------------------------------
Route::get('/dashboard', function () {
    return view('dashboard'); // créer resources/views/dashboard.blade.php
})->middleware(['auth'])->name('dashboard');

// ----------------------------------------------------------------------
// --- Routes Profil Utilisateur ---
// ----------------------------------------------------------------------
Route::middleware('auth')->group(function () {
    // Affiche le formulaire de profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    // Met à jour le profil
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Supprime le compte
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ----------------------------------------------------------------------
// --- Auth routes (Register/Login/Logout) ---
// ----------------------------------------------------------------------
require __DIR__.'/auth.php';
