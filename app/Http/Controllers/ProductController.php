<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage as Image; 
use App\Models\Category; // Import nécessaire
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; 

class ProductController extends Controller
{
    
    public function __construct() 
    {
        // Exemple : $this->middleware('auth');
    }

    // Affiche la liste des produits dans l'administration
    public function index()
    {
        $products = Product::with(['images', 'category'])->get(); 
        return view('admin.products.index', compact('products'));
    }

    // Affiche le formulaire de création
    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Enregistre un nouveau produit, gère l'upload de l'image et la création des variantes
     */
    public function store(Request $request)
    {
        // 1. Validation des données
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'description' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            
            'images' => 'required|array|min:1', 
            'images.*.file' => 'required|file|mimes:jpeg,png,jpg,gif,webp|max:2048', 
            'images.*.variant_name' => 'required|string|max:100', 
        ]);

        // 2. Création du produit principal
        $productData = [
            'name' => $validatedData['name'],
            'slug' => Str::slug($validatedData['name']),
            'price' => $validatedData['price'],
            'stock_quantity' => $validatedData['stock_quantity'],
            'description' => $validatedData['description'],
            'category_id' => $validatedData['category_id'],
            'is_published' => $request->has('is_published'),
        ];

        $product = Product::create($productData);

        // 3. Enregistrement des images AVEC leurs variantes
        $isFirstImage = true;
        
        foreach ($request->images as $imageInput) {
            
            if (isset($imageInput['file']) && $imageInput['file'] instanceof \Illuminate\Http\UploadedFile && $imageInput['file']->isValid()) {
                
                $path = $imageInput['file']->store('products/gallery', 'public');
                
                $product->images()->create([
                    'path' => $path,
                    'variant_name' => $imageInput['variant_name'], 
                    'is_main' => $isFirstImage 
                ]);
                
                $isFirstImage = false;
            }
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Le produit "' . $product->name . '" a été créé avec succès et les variantes ont été enregistrées !');
    }

    /**
     * Affiche la fiche produit (côté front-end)
     * ✅ CORRIGÉ : Envoi de $categories à la vue.
     */
    public function show(Product $product)
    {
        $product->load('category', 'images');
        
        // --- 🔑 CORRECTION DE L'ERREUR CRITIQUE ---
        // Récupère les catégories actives pour la navigation (barre latérale, menu)
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        // ------------------------------------------

        return view('products.show', compact('product', 'categories'));
    }

    // Affiche le formulaire de modification
    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Met à jour le produit existant et gère l'ajout de nouvelles variantes
     */
    public function update(Request $request, Product $product)
    {
        // 1. Validation des données du produit (texte et nouvelles images/variantes)
        $rules = [
            'name' => 'required|string|max:255|unique:products,name,' . $product->id, 
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'description' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
        ];
        
        // Validation des NOUVELLES images
        if ($request->has('new_images')) {
            $rules['new_images'] = 'array';
            $rules['new_images.*.file'] = 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:2048'; 
            $rules['new_images.*.variant_name'] = 'required_with:new_images.*.file|string|max:100'; 
        }

        $validatedData = $request->validate($rules);

        // 2. Mise à jour du produit (texte)
        $productData = collect($validatedData)->except('new_images')->toArray(); 
        $productData['slug'] = Str::slug($productData['name']);
        $productData['is_published'] = $request->has('is_published');
        
        $product->update($productData);

        // 3. Traitement des NOUVELLES images (si elles existent)
        if ($request->has('new_images')) {
            foreach ($request->new_images as $imageInput) {
                
                if (isset($imageInput['file']) && $imageInput['file'] instanceof \Illuminate\Http\UploadedFile && $imageInput['file']->isValid()) {
                    
                    $path = $imageInput['file']->store('products/gallery', 'public');
                    
                    $product->images()->create([
                        'path' => $path,
                        'is_main' => false,
                        'variant_name' => $imageInput['variant_name'] ?? null, 
                    ]);
                }
            }
        }

        // 4. Redirection vers la page d'édition pour voir les changements
        return redirect()
            ->route('admin.products.edit', $product) 
            ->with('success', 'Le produit "' . $product->name . '" a été mis à jour avec succès et les nouvelles images ont été traitées !');
    }

    /**
     * Supprime le produit et ses fichiers images associés.
     */
    public function destroy(Product $product)
    {
        $productName = $product->name;
        
        // Supprime les fichiers physiques
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
        }
        
        // Supprime le produit (les images associées sont supprimées par cascade de la BDD)
        $product->delete(); 

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Le produit "' . $productName . '" a été supprimé avec succès.');
    }

    /**
     * Supprime une seule image de la galerie d'un produit.
     */
    public function destroyImage(int $id)
    {
        $image = Image::findOrFail($id);
        $product = $image->product; 

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'L\'image a été supprimée avec succès.');
    }

    /**
     * Définit une image comme principale pour un produit.
     */
    public function setMainImage(int $id)
    {
        $image = Image::findOrFail($id);
        $product = $image->product; 

        // Réinitialise les autres
        $product->images()->update(['is_main' => false]);
        // Définit la nouvelle principale
        $image->update(['is_main' => true]);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'L\'image principale a été mise à jour !');
    }
}