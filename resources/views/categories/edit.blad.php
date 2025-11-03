<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la Catégorie : {{ $category->name }}</title>
    
    {{-- ✅ LIAISON CSS EXTERNE --}}
    @vite(['resources/css/admin.css'])
</head>
<body>

    {{-- ✅ CONTENEUR GLOBAL POUR LE STYLE --}}
    <div class="container">
    
        {{-- ✅ LIEN DE RETOUR (Utilise la classe 'back-link' et la route admin.) --}}
        <a href="{{ route('admin.categories.index') }}" class="back-link">
             ← Retour à la liste des catégories
        </a>

        <h1>Modifier la Catégorie : {{ $category->name }}</h1>
        
        {{-- 🛑 ATTENTION : Routes corrigées en admin.categories.update --}}
        <form action="{{ route('admin.categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT') {{-- Indique que c'est une requête de mise à jour --}}

            {{-- Nom --}}
            <div>
                <label for="name">Nom de la Catégorie :</label>
                {{-- old() utilise la valeur du modèle $category->name si old() est vide --}}
                <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                @error('name') <p class="error">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description">Description :</label>
                <textarea id="description" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                @error('description') <p class="error">{{ $message }}</p> @enderror
            </div>

            {{-- État (Actif) --}}
            <div>
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                <label for="is_active" style="display: inline-block; margin-left: 5px;">Rendre cette catégorie active (visible publiquement)</label>
                @error('is_active') <p class="error">{{ $message }}</p> @enderror
            </div>

            <button type="submit">Enregistrer les Modifications</button>
        </form>
    
    </div>

</body>
</html>