<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer une Catégorie</title>
    
    {{-- ✅ LIAISON CSS EXTERNE --}}
    @vite(['resources/css/admin.css'])
</head>
<body>

    {{-- ✅ CONTENEUR GLOBAL POUR LE STYLE (Défini dans app.css) --}}
    <div class="container">
    
        {{-- ✅ LIEN DE RETOUR (Utilise la classe 'back-link' et la route admin.) --}}
        <a href="{{ route('admin.categories.index') }}" class="back-link">
             ← Retour à la liste des catégories
        </a>

        <h1>Créer une Nouvelle Catégorie</h1>
        
        {{-- 🛑 ATTENTION : Route corrigée en admin.categories.store --}}
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf

            {{-- Nom --}}
            <div>
                <label for="name">Nom de la Catégorie :</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                @error('name') <p class="error">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description">Description :</label>
                <textarea id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description') <p class="error">{{ $message }}</p> @enderror
            </div>

            {{-- État (Actif) --}}
            <div>
                {{-- Le 'display: inline' n'est plus nécessaire grâce au CSS externe --}}
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <label for="is_active" style="display: inline-block; margin-left: 5px;">Rendre cette catégorie active (visible publiquement)</label>
                @error('is_active') <p class="error">{{ $message }}</p> @enderror
            </div>

            <button type="submit">Enregistrer la Catégorie</button>
        </form>
    
    </div>

</body>
</html>