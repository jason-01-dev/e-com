<!-- create.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un Nouveau Produit</title>
    @vite(['resources/css/admin.css'])
</head>
<body>
<div class="container">

    <a href="{{ route('admin.products.index') }}" class="back-link">
         ← Retour à la liste des produits
    </a>

    <h1>Ajouter un Nouveau Produit</h1>

    @if ($errors->any())
        <div class="validation-errors">
            <strong>Veuillez corriger les erreurs suivantes :</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Nom --}}
        <div>
            <label for="name">Nom du Produit :</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            @error('name') <p class="error">{{ $message }}</p> @enderror
        </div>

        {{-- Catégorie --}}
        <div>
            <label for="category_id">Catégorie :</label>
            <select id="category_id" name="category_id">
                <option value="">-- Sélectionner une catégorie (Optionnel) --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id') <p class="error">{{ $message }}</p> @enderror
        </div>

        {{-- Prix --}}
        <div>
            <label for="price">Prix :</label>
            <input type="number" id="price" name="price" step="0.01" min="0" value="{{ old('price') }}" required>
            @error('price') <p class="error">{{ $message }}</p> @enderror
        </div>

        {{-- Stock --}}
        <div>
            <label for="stock_quantity">Quantité en Stock :</label>
            <input type="number" id="stock_quantity" name="stock_quantity" min="0" value="{{ old('stock_quantity') }}" required>
            @error('stock_quantity') <p class="error">{{ $message }}</p> @enderror
        </div>

        {{-- Tailles disponibles --}}
        <div>
            <label for="sizes">Tailles Disponibles :</label>
            <input type="text" id="sizes" name="sizes" placeholder="Ex: 38,39,40" value="{{ old('sizes') }}">
            <small style="color: #666;">Séparez les tailles par des virgules. Exemple : 38,39,40</small>
            @error('sizes') <p class="error">{{ $message }}</p> @enderror
        </div>

        {{-- Description --}}
        <div>
            <label for="description">Description :</label>
            <textarea id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
            @error('description') <p class="error">{{ $message }}</p> @enderror
        </div>

        <hr style="margin: 20px 0;">

        {{-- 🛑 SECTION IMAGES ET VARIANTES 🛑 --}}
        <div class="image-variants-section" style="border: 1px solid #ddd; padding: 20px; margin-bottom: 20px;">
            <h2>Gestion des Images et des Variantes</h2>
            <p style="margin-bottom: 15px; color: #666;">Associez une variante (couleur, taille, etc.) à chaque image. **Ceci est crucial pour le carrousel dynamique.**</p>

            {{-- Bloc 1 : Image Principale (Requis) --}}
            <div class="image-pair-group" style="margin-bottom: 15px; border-left: 3px solid #4CAF50; padding-left: 10px;">
                <label>Image 1 (Principale) :</label>
                <input type="file" name="images[0][file]" required>
                
                <label for="variant_name_0">Variante 1 (Ex: Rouge) :</label>
                <input type="text" id="variant_name_0" name="images[0][variant_name]" value="{{ old('images.0.variant_name') }}" placeholder="Ex: Rouge" required>
                @error('images.0.file') <p class="error">{{ $message }}</p> @enderror
                @error('images.0.variant_name') <p class="error">{{ $message }}</p> @enderror
            </div>
            
            {{-- Bloc 2 : Image Secondaire (Optionnel) --}}
            <div class="image-pair-group" style="margin-bottom: 15px; border-left: 3px solid #2196F3; padding-left: 10px;">
                <label>Image 2 :</label>
                <input type="file" name="images[1][file]">
                
                <label for="variant_name_1">Variante 2 (Ex: Bleu) :</label>
                <input type="text" id="variant_name_1" name="images[1][variant_name]" value="{{ old('images.1.variant_name') }}" placeholder="Ex: Bleu">
                @error('images.1.file') <p class="error">{{ $message }}</p> @enderror
                @error('images.1.variant_name') <p class="error">{{ $message }}</p> @enderror
            </div>

            {{-- Bloc 3 : Image Tertiaire (Optionnel) --}}
            <div class="image-pair-group" style="margin-bottom: 15px; border-left: 3px solid #FFC107; padding-left: 10px;">
                <label>Image 3 :</label>
                <input type="file" name="images[2][file]">
                
                <label for="variant_name_2">Variante 3 (Ex: Vert) :</label>
                <input type="text" id="variant_name_2" name="images[2][variant_name]" value="{{ old('images.2.variant_name') }}" placeholder="Ex: Vert">
                @error('images.2.file') <p class="error">{{ $message }}</p> @enderror
                @error('images.2.variant_name') <p class="error">{{ $message }}</p> @enderror
            </div>
        </div>

        <hr style="margin: 20px 0;">

        {{-- Publié --}}
        <div>
            <input type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }}>
            <label for="is_published" style="display: inline-block; margin-left: 5px;">Publier le produit immédiatement</label>
            @error('is_published') <p class="error">{{ $message }}</p> @enderror
        </div>

        <button type="submit">Enregistrer le Produit</button>
    </form>
    
</div>
</body>
</html>
