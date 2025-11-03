<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    /**
     * Affiche la liste des produits publiés (Homepage/Catalogue).
     */
    public function index()
    {
        // 1. Récupère tous les produits publiés (sans filtre de catégorie)
        $products = Product::where('is_published', true)
                            ->with(['images', 'category']) 
                            ->latest()
                            // ✅ CORRECTION MAJEURE : Utilisation de paginate pour la performance et les liens de navigation
                            ->paginate(12); // Par exemple, 12 produits par page

        // 2. Récupère toutes les catégories actives (pour le menu de navigation)
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        // 3. Renvoie la vue. La variable $category (pour le titre) n'est pas passée ici.
        return view('front.index', compact('products', 'categories'));
    }
    
    /**
     * Affiche le catalogue filtré pour une catégorie spécifique.
     * La route doit ressembler à : Route::get('/category/{category:slug}', [FrontController::class, 'category'])->name('front.category');
     */
    public function category(Category $category)
    {
        // S'assurer que la catégorie est active publiquement (la vérification est bonne)
        if (!$category->is_active) {
            abort(404);
        }

        // 1. Récupère les produits publiés appartenant à cette catégorie
        $products = Product::where('is_published', true)
                            ->where('category_id', $category->id)
                            ->with(['images', 'category'])
                            ->latest()
                            // ✅ CORRECTION MAJEURE : Utilisation de paginate et withQueryString pour les liens de pagination
                            ->paginate(12)
                            ->withQueryString(); // Garde le slug de la catégorie dans l'URL des pages suivantes

        // 2. Récupère toutes les catégories actives (pour le menu de navigation)
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        
        // 3. Passe les produits, toutes les catégories et la catégorie active ($category) à la vue
        return view('front.index', compact('products', 'categories', 'category'));
    }

    /**
     * Affiche la page de détail d'un produit.
     * NOTE : Pour le front-end, nous utilisons l'injection de Product par le slug dans la route.
     * La route doit ressembler à : Route::get('/product/{product:slug}', [FrontController::class, 'show'])->name('front.product.show');
     */
    public function show(Product $product)
    {
        // On vérifie que le produit est publié
        if (!$product->is_published) {
             abort(404);
        }

        // Récupère les catégories pour la sidebar, comme sur la page catalogue
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('front.product.show', [ 
            'product' => $product,
            'categories' => $categories
        ]);
    }
}