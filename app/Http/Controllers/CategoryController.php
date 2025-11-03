<?php

namespace App\Http\Controllers;

use App\Models\Category; // Importe le modèle Category
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Pour la création de slugs manuelle si besoin

class CategoryController extends Controller
{
    /**
     * Affiche la liste des catégories (READ).
     */
    public function index()
    {
        // Récupère toutes les catégories de la base de données
        $categories = Category::orderBy('name')->get();
        
        // Retourne la vue d'index (qui sera dans resources/views/categories/index.blade.php)
        return view('categories.index', compact('categories'));
    }

    /**
     * Affiche le formulaire de création de catégorie (CREATE).
     */
    public function create()
    {
        // Retourne la vue du formulaire de création (resources/views/categories/create.blade.php)
        return view('categories.create');
    }

    /**
     * Stocke une nouvelle catégorie dans la base de données (STORE).
     */
    public function store(Request $request)
    {
        // 1. Validation des données
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // 2. Préparation des données (y compris le slug)
        $category = new Category();
        $category->name = $validatedData['name'];
        $category->slug = Str::slug($validatedData['name']); // Génère un slug à partir du nom
        $category->description = $validatedData['description'];
        $category->is_active = $request->has('is_active'); // Case à cocher pour le statut

        // 3. Sauvegarde et redirection
        $category->save();

        return redirect()->route('admin.categories.index')
                         ->with('success', 'La catégorie ' . $category->name . ' a été créée avec succès !');
    }

    /**
     * Affiche la catégorie spécifiée. Non utilisé pour l'admin (admin.categories.show est "excepted").
     */
    public function show(Category $category)
    {
        // Ne devrait pas être appelé car nous avons exclu 'show' dans routes/web.php
    }

    /**
     * Affiche le formulaire d'édition de la catégorie (EDIT).
     */
    public function edit(Category $category)
    {
        // Retourne la vue d'édition (resources/views/categories/edit.blade.php) avec la catégorie à modifier
        return view('categories.edit', compact('category'));
    }

    /**
     * Met à jour la catégorie dans la base de données (UPDATE).
     */
    public function update(Request $request, Category $category)
    {
        // 1. Validation des données
        $validatedData = $request->validate([
            // La validation 'unique' doit ignorer la catégorie actuelle
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // 2. Mise à jour
        $category->name = $validatedData['name'];
        $category->slug = Str::slug($validatedData['name']); // Mise à jour du slug
        $category->description = $validatedData['description'];
        $category->is_active = $request->has('is_active');
        
        $category->save();

        // 3. Redirection
        return redirect()->route('admin.categories.index')
                         ->with('success', 'La catégorie ' . $category->name . ' a été mise à jour avec succès.');
    }

    /**
     * Supprime la catégorie de la base de données (DELETE).
     */
    public function destroy(Category $category)
    {
        $categoryName = $category->name;
        $category->delete();

        return redirect()->route('admin.categories.index')
                         ->with('success', 'La catégorie ' . $categoryName . ' a été supprimée.');
    }
}