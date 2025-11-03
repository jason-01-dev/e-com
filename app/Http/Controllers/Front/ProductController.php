<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage as Image;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct()
    {
        // Exemple : $this->middleware('auth');
    }

    // Liste des produits
    public function index()
    {
        $products = Product::with(['images', 'category'])->get();
        return view('admin.products.index', compact('products'));
    }

    // Formulaire création
    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    // Enregistrement nouveau produit
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'sizes' => 'nullable|string|max:255', // 🆕 Gestion des tailles
            'description' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',

            'images' => 'required|array|min:1',
            'images.*.file' => 'required|file|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'images.*.variant_name' => 'required|string|max:100',
        ]);

        // Création produit
        $productData = [
            'name' => $validatedData['name'],
            'slug' => Str::slug($validatedData['name']),
            'price' => $validatedData['price'],
            'stock_quantity' => $validatedData['stock_quantity'],
            'sizes' => $validatedData['sizes'] ?? null, // 🆕
            'description' => $validatedData['description'],
            'category_id' => $validatedData['category_id'],
            'is_published' => $request->has('is_published'),
        ];

        $product = Product::create($productData);

        // Enregistrement des images + variantes
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
            ->with('success', 'Le produit "' . $product->name . '" a été créé avec succès !');
    }

    // Formulaire modification
    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    // Mise à jour produit
    public function update(Request $request, Product $product)
    {
        $rules = [
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'sizes' => 'nullable|string|max:255', // 🆕
            'description' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
        ];

        if ($request->has('new_images')) {
            $rules['new_images'] = 'array';
            $rules['new_images.*.file'] = 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:2048';
            $rules['new_images.*.variant_name'] = 'required_with:new_images.*.file|string|max:100';
        }

        $validatedData = $request->validate($rules);

        // Mise à jour produit
        $productData = collect($validatedData)->except('new_images')->toArray();
        $productData['slug'] = Str::slug($productData['name']);
        $productData['is_published'] = $request->has('is_published');

        $product->update($productData);

        // Traitement nouvelles images
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

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Le produit "' . $product->name . '" a été mis à jour avec succès !');
    }

    // Suppression produit
    public function destroy(Product $product)
    {
        $productName = $product->name;
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
        }
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Le produit "' . $productName . '" a été supprimé avec succès.');
    }

    // Supprimer une image
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

    // Définir image principale
    public function setMainImage(int $id)
    {
        $image = Image::findOrFail($id);
        $product = $image->product;

        $product->images()->update(['is_main' => false]);
        $image->update(['is_main' => true]);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'L\'image principale a été mise à jour !');
    }
}
