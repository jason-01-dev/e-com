<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration des Catégories</title>
    
    {{-- ✅ LIAISON CSS EXTERNE --}}
    @vite(['resources/css/admin.css'])
</head>
<body>

    <div class="container">
        
        <h1>Gestion des Catégories</h1>

        {{-- 🛑 CORRECTION ROUTE : admin.categories.create --}}
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Ajouter une Nouvelle Catégorie</a>
        
        @if (session('success'))
            <p class="flash-success">{{ session('success') }}</p>
        @endif

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Slug</th>
                    <th>Description</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->slug }}</td>
                        <td>{{ Str::limit($category->description, 50) }}</td>
                        <td>
                            @if ($category->is_active)
                                {{-- Utilisation des classes CSS pour le statut --}}
                                <span class="status-active">Active</span>
                            @else
                                <span class="status-inactive">Inactive</span>
                            @endif
                        </td>
                        <td>
                            {{-- 🛑 CORRECTION ROUTE : admin.categories.edit --}}
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-edit">Modifier</a>
                            
                            {{-- 🛑 CORRECTION ROUTE : admin.categories.destroy --}}
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-delete">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Aucune catégorie n'a été trouvée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- ✅ LIEN DE RETOUR CORRIGÉ (products.index est ici une route admin, si vous voulez revenir au dashboard, utilisez 'dashboard') --}}
        <a href="{{ route('admin.products.index') }}" class="back-link">← Retour à la liste des produits</a>
    </div>

</body>
</html>