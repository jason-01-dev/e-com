<!-- edit.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le Produit : {{ $product->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    {{-- Script pour ajout dynamique de nouvelles images --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let imageIndex = 2; // Commence à 2 car les index 0 et 1 sont déjà dans le HTML

            const addButton = document.getElementById('add-image-field');
            if (addButton) {
                addButton.addEventListener('click', function() {
                    const container = document.getElementById('new-images-container');
                    
                    const newField = document.createElement('div');
                    newField.classList.add('image-pair-group');
                    newField.style.cssText = 'margin-bottom: 15px; border-left: 3px solid #2196F3; padding-left: 10px;';
                    newField.innerHTML = `
                        <label>Nouvelle Image ${imageIndex + 1} :</label>
                        <input type="file" name="new_images[${imageIndex}][file]">
                        <label for="new_variant_name_${imageIndex}">Variante ${imageIndex + 1} :</label>
                        <input type="text" id="new_variant_name_${imageIndex}" name="new_images[${imageIndex}][variant_name]" placeholder="Ex: Couleur, Motif">
                        <button type="button" class="btn btn-delete-small remove-image-field" style="margin-top: 5px;">Retirer</button>
                    `;
                    container.appendChild(newField);
                    imageIndex++;
                });
            }
            
            const newImagesContainer = document.getElementById('new-images-container');
            if (newImagesContainer) {
                newImagesContainer.addEventListener('click', function(e) {
                    if (e.target.classList.contains('remove-image-field')) {
                        e.target.closest('.image-pair-group').remove();
                    }
                });
            }
        });
    </script>
</head>
<body>

<div class="container">

    <a href="{{ route('admin.products.index') }}" class="back-link">
        ← Retour à la liste des produits
    </a>

    <h1>Modifier le Produit : {{ $product->name }}</h1>

    @if (session('success'))
        <p class="flash-success">{{ session('success') }}</p>
    @endif

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

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Nom --}}
        <div>
            <label for="name">Nom du Produit :</label>
            <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required>
            @error('name') <p class="error">{{ $message }}</p> @enderror
        </div>

        {{-- Catégorie --}}
        <div>
            <label for="category_id">Catégorie :</label>
            <select id="category_id" name="category_id">
                <option value="">-- Sélectionner une catégorie (Optionnel) --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id') <p class="error">{{ $message }}</p> @enderror
        </div>

        {{-- Prix --}}
        <div>
            <label for="price">Prix :</label>
            <input type="number" id="price" name="price" step="0.01" min="0" value="{{ old('price', $product->price) }}" required>
            @error('price') <p class="error">{{ $message }}</p> @enderror
        </div>

        {{-- Stock --}}
        <div>
            <label for="stock_quantity">Quantité en Stock :</label>
            <input type="number" id="stock_quantity" name="stock_quantity" min="0" value="{{ old('stock_quantity', $product->stock_quantity) }}" required>
            @error('stock_quantity') <p class="error">{{ $message }}</p> @enderror
        </div>

        {{-- Tailles disponibles --}}
        <div>
            <label for="sizes">Tailles Disponibles :</label>
            <input type="text" id="sizes" name="sizes" placeholder="Ex: 38,39,40" value="{{ old('sizes', $product->sizes) }}">
            <small style="color: #666;">Séparez les tailles par des virgules. Exemple : 38,39,40</small>
            @error('sizes') <p class="error">{{ $message }}</p> @enderror
        </div>

        {{-- Description --}}
        <div>
            <label for="description">Description :</label>
            <textarea id="description" name="description" rows="5" required>{{ old('description', $product->description) }}</textarea>
            @error('description') <p class="error">{{ $message }}</p> @enderror
        </div>

        <hr style="margin: 20px 0;">

        {{-- Galerie existante --}}
        <div class="current-gallery" style="border: 1px solid #ccc; padding: 20px; margin-bottom: 20px;">
            <h2>Images Actuelles</h2>
            
            @forelse ($product->images as $image)
                <div class="image-item" style="border: 1px solid {{ $image->is_main ? '#4CAF50' : '#ddd' }}; padding: 10px; margin-bottom: 15px;">
                    <img src="{{ Storage::url($image->path) }}" alt="{{ $product->name }} image {{ $loop->iteration }}" style="width: 150px; height: auto;">
                    <p><strong>Variante:</strong> {{ $image->variant_name }}</p>

                    @if ($image->is_main)
                        <small class="status-active" style="color: #4CAF50;">(Image Principale)</small>
                    @else
                        <button type="button" onclick="document.getElementById('set-main-{{ $image->id }}').submit();" class="btn btn-warning-small">
                            Définir comme principal
                        </button>
                    @endif

                    <button type="button" onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette image ?')) document.getElementById('delete-img-{{ $image->id }}').submit();" class="btn btn-delete-small">
                        Supprimer
                    </button>
                </div>
            @empty
                <p>Aucune image n'a été ajoutée pour ce produit.</p>
            @endforelse
        </div>

        <hr style="margin: 20px 0;">

        {{-- Ajouter de nouvelles images --}}
        <div class="new-images-section" style="border: 1px solid #2196F3; padding: 20px; margin-bottom: 20px;">
            <h2>Ajouter de Nouvelles Images et Variantes</h2>
            <p style="margin-bottom: 15px; color: #666;">Ajoutez les fichiers et leur nom de variante (ex: Couleur, Motif).</p>

            <div id="new-images-container">
                <div class="image-pair-group" style="margin-bottom: 15px; border-left: 3px solid #2196F3; padding-left: 10px;">
                    <label>Nouvelle Image 1 :</label>
                    <input type="file" name="new_images[0][file]">
                    <label for="new_variant_name_0">Variante 1 :</label>
                    <input type="text" id="new_variant_name_0" name="new_images[0][variant_name]" placeholder="Ex: Noir" value="{{ old('new_images.0.variant_name') }}">
                    @error('new_images.0.file') <p class="error">{{ $message }}</p> @enderror
                    @error('new_images.0.variant_name') <p class="error">{{ $message }}</p> @enderror
                </div>

                <div class="image-pair-group" style="margin-bottom: 15px; border-left: 3px solid #FFC107; padding-left: 10px;">
                    <label>Nouvelle Image 2 :</label>
                    <input type="file" name="new_images[1][file]">
                    <label for="new_variant_name_1">Variante 2 :</label>
                    <input type="text" id="new_variant_name_1" name="new_images[1][variant_name]" placeholder="Ex: Vert Militaire" value="{{ old('new_images.1.variant_name') }}">
                    @error('new_images.1.file') <p class="error">{{ $message }}</p> @enderror
                    @error('new_images.1.variant_name') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>

            <button type="button" id="add-image-field" class="btn btn-secondary" style="margin-top: 5px;">+ Ajouter un champ d'image</button>
        </div>

        <hr style="margin: 20px 0;">

        {{-- Publié --}}
        <div>
            <input type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published', $product->is_published) ? 'checked' : '' }}>
            <label for="is_published" style="display: inline-block; margin-left: 5px;">Publier le produit immédiatement</label>
            @error('is_published') <p class="error">{{ $message }}</p> @enderror
        </div>

        <button type="submit" id="submit-btn" class="btn btn-primary" style="margin-top: 20px;">Enregistrer les Modifications</button>
    </form>

    {{-- Formulaires action image principale / suppression --}}
    @foreach ($product->images as $image)
        <form id="set-main-{{ $image->id }}" action="{{ route('admin.product_images.set_main', $image) }}" method="POST" style="display:none;">
            @csrf
            @method('PUT')
        </form>

        <form id="delete-img-{{ $image->id }}" action="{{ route('admin.product_images.destroy', $image) }}" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

</div>
</body>
</html>
