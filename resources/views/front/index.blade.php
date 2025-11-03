<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Boutique en Ligne | Catalogue</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<header class="main-header">
    <div class="header-content cart-header">
        <a href="{{ route('front.index') }}" class="catalog-link">Catalogue</a>
        <a href="{{ route('cart.index') }}" class="cart-summary back-link">
            Mon Panier (<span class="cart-summary-value">{{ \Cart::getTotalQuantity() ?? 0 }}</span>)
        </a>
    </div>
</header>

<div class="container">
    <div class="page-content-container">

        <aside class="sidebar">
            <div class="category-list">
                <h2>Filtrer par Catégorie</h2>
                <ul>
                    <li>
                        <a href="{{ route('front.index') }}" class="{{ !request('category') ? 'active-filter' : '' }}">Toutes les Catégories</a>
                    </li>
                    @foreach ($categories as $cat)
                        <li>
                            <a href="{{ route('front.index', ['category' => $cat->slug]) }}" class="{{ request('category') === $cat->slug ? 'active-filter' : '' }}">
                                {{ $cat->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </aside>

        <main class="catalogue">

            @if (isset($currentCategory))
                <h1>Catalogue : {{ $currentCategory->name }}</h1>
                <div class="current-filter">
                    Affichage des produits dans la catégorie : <strong>{{ $currentCategory->name }}</strong>. 
                    <a href="{{ route('front.index') }}" class="back-link">Voir tout</a>
                </div>
            @else
                <h1>Tous nos Produits</h1>
            @endif

            <div class="product-grid">
                @forelse ($products as $product)
                    <div class="product-card">
                        @php
                            $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->sortBy('id')->first();
                            $imagePath = $mainImage ? Storage::url($mainImage->path) : 'https://via.placeholder.com/250x250?text=Image+Manquante';
                            $variants = $product->images->pluck('variant_name')->filter()->unique();
                            $sizes = $product->available_sizes ? explode(',', $product->available_sizes) : [];
                        @endphp
                        <div class="product-image-wrapper">
                            <a href="{{ route('front.product.show', $product->slug) }}">
                                <img src="{{ $imagePath }}" alt="{{ $product->name }}">
                            </a>
                        </div>

                        <h3><a href="{{ route('front.product.show', $product->slug) }}">{{ $product->name }}</a></h3>

                        <p class="category-info">
                            Catégorie :
                            @if ($product->category)
                                <a href="{{ route('front.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a>
                            @else
                                Non classé
                            @endif
                        </p>

                        <p class="price">{{ number_format($product->price, 2, ',', ' ') }} €</p>

                        @if ($product->stock_quantity > 0)
                            <form action="{{ route('cart.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">

                                {{-- Variante --}}
                                @if ($variants->count() > 1)
                                    <select name="variant_name" required>
                                        @foreach($variants as $variant)
                                            <option value="{{ $variant }}">{{ $variant }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="hidden" name="variant_name" value="{{ $variants->first() ?? 'Standard' }}">
                                @endif

                                {{-- Taille --}}
                                @if(count($sizes) > 0)
                                    <select name="size" required>
                                        @foreach($sizes as $size)
                                            <option value="{{ $size }}">{{ $size }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="hidden" name="size" value="Standard">
                                @endif

                                <button type="submit" class="btn add-to-cart">Ajouter au panier</button>
                            </form>
                        @else
                            <button class="btn add-to-cart disabled" disabled>Rupture de Stock</button>
                        @endif
                    </div>
                @empty
                    <p style="grid-column: 1 / -1; text-align: center;">Aucun produit publié pour le moment.</p>
                @endforelse
            </div>

            <div class="pagination-links" style="margin-top: 30px;">
                {{ $products->links() }}
            </div>

        </main>
    </div>
</div>

</body>
</html>
