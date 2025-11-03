<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration des Produits</title>
    
    {{-- ✅ LIAISON CSS EXTERNE : Utilisation de admin.css --}}
    @vite(['resources/css/admin.css']) 
</head>
<body>

    {{-- ✅ CONTENEUR GLOBAL POUR LE STYLE (Défini dans admin.css) --}}
    <div class="container"> 

        <h1>Liste des Produits</h1>

        {{-- Bouton Créer un nouveau produit --}}
        <div class="add-button">
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Créer un nouveau produit</a>
        </div>

        {{-- Message de succès --}}
        @if (session('success'))
            {{-- Utilise la classe CSS pour le message de succès --}}
            <div class="flash-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($products->isEmpty())
            <p class="text-center">Aucun produit trouvé dans votre catalogue.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Photo</th> 
                        <th>Nom</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>Publié</th>
                        <th>Actions</th> 
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            
                            {{-- CELLULE PHOTO --}}
                            <td>
                                @php
                                    // Tente de trouver l'image principale ou la première
                                    $mainImage = $product->images->where('is_main', true)->first() 
                                                            ?? $product->images->first(); 
                                @endphp

                                @if ($mainImage)
                                    {{-- ✅ Utilise la classe CSS 'product-thumb' --}}
                                    <img src="{{ asset('storage/' . $mainImage->path) }}" 
                                        alt="Photo de {{ $product->name }}" 
                                        class="product-thumb">
                                @else
                                    <span class="status-inactive">Pas d'image</span>
                                @endif
                            </td>

                            <td>{{ $product->name }}</td>
                            <td>{{ number_format($product->price, 2) }} €</td>
                            <td>{{ $product->stock_quantity }}</td>
                            <td>
                                {{-- ✅ Utilise les classes de statut --}}
                                @if ($product->is_published)
                                    <span class="status-active">Oui</span>
                                @else
                                    <span class="status-inactive">Non</span>
                                @endif
                            </td>
                            
                            {{-- CELLULE D'ACTION (Éditer + Supprimer) --}}
                            <td> 
                                {{-- 1. Lien d'édition (Utilise la classe 'btn-edit') --}}
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-edit">Éditer</a>

                                {{-- 2. Formulaire de suppression (Utilise la classe 'btn-delete') --}}
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display:inline-block; margin-left: 5px;" 
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                                    @csrf
                                    @method('DELETE') 
                                    
                                    {{-- Le bouton utilise la classe btn-delete pour le style --}}
                                    <button type="submit" class="btn btn-delete">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    </div> {{-- Fin du div.container --}}

    {{-- Optionnel: lien retour à l'index des catégories --}}
    <a href="{{ route('admin.categories.index') }}" class="back-link">← Retour à la gestion des catégories</a>

</body>
</html>