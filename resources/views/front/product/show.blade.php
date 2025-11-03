<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $product->name }} | Détail</title>

    {{-- ESSENTIEL : Utilise la directive Vite pour charger le CSS et le JS (qui inclut Swiper) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js']) 
</head>
<body>

    {{-- Header --}}
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

            {{-- Sidebar Catégories --}}
            <aside class="sidebar">
                <div class="category-list">
                    <h2>Filtrer par Catégorie</h2>
                    <ul>
                        <li><a href="{{ route('front.index') }}">Toutes les Catégories</a></li> 
                        {{-- 🛑 ATTENTION : La variable $categories doit toujours être envoyée par le ProductController@show --}}
                        @if(isset($categories))
                            @foreach ($categories as $cat)
                                <li><a href="{{ route('front.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a></li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </aside>

            <main class="product-detail">

                <h1>{{ $product->name }}</h1>
                <a href="{{ route('front.index') }}" class="back-link">← Retour au Catalogue</a>

                <div class="product-container">
                    
                    {{-- Colonne de la Galerie (Carousels Swiper) --}}
                    <div class="product-gallery">
                        
                        {{-- SLIDER PRINCIPAL --}}
                        <div id="main-slider" class="swiper main-slider">
                            <div class="swiper-wrapper">
                                @foreach ($product->images->sortBy('id') as $image)
                                    <div class="swiper-slide">
                                        <img src="{{ Storage::url($image->path) }}" alt="{{ $product->name }} - {{ $image->variant_name ?? 'Image' }}">
                                    </div>
                                @endforeach
                                @if($product->images->isEmpty())
                                    <div class="swiper-slide">
                                        <img src="https://via.placeholder.com/500x450?text=Pas+d'image" alt="Image par défaut">
                                    </div>
                                @endif
                            </div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>

                        {{-- SLIDER MINIATURES --}}
                        @if($product->images->count() > 1)
                            <div id="thumbnails-slider" class="swiper thumbnails-slider">
                                <div class="swiper-wrapper">
                                    @foreach ($product->images->sortBy('id') as $image)
                                        <div class="swiper-slide">
                                            <img src="{{ Storage::url($image->path) }}" alt="Thumbnail {{ $loop->index + 1 }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div> {{-- /product-gallery --}}

                    {{-- Colonne des Informations --}}
                    <div class="product-info">
                        
                        <p class="category-info">
                            **Catégorie :** @if ($product->category)
                                <a href="{{ route('front.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a>
                            @else
                                *Non classé*
                            @endif
                        </p>

                        <div class="price">**{{ number_format($product->price, 2, ',', ' ') }} €**</div>

                        <p class="{{ $product->stock_quantity > 0 ? 'stock-in' : 'stock-out' }}">
                            {{ $product->stock_quantity > 0 ? '✅ En Stock ('.$product->stock_quantity.' restants)' : '❌ Rupture de Stock' }}
                        </p>

                        <h2>Description</h2>
                        <p>{{ $product->description }}</p>

                        {{-- Formulaire ajout au panier CORRIGÉ --}}
                        <div class="cart-form-wrapper">
                            @if ($product->stock_quantity > 0)
                                <form action="{{ route('cart.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                                    {{-- ✅ DEBUT DE LA CORRECTION : Ajout du sélecteur de variantes --}}
                                    @php
                                        // Récupère les noms de variantes uniques
                                        $variants = $product->images->pluck('variant_name')->filter()->unique();
                                    @endphp
                                    
                                    @if ($variants->count() > 1)
                                        <div class="variant-select">
                                            <label for="variant_name">Variante :</label>
                                            <select name="variant_name" id="variant_name" required>
                                                @foreach($variants as $variant)
                                                    <option value="{{ $variant }}">{{ $variant }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @else 
                                        {{-- Variante par défaut si une seule ou aucune n'est définie --}}
                                        <input type="hidden" name="variant_name" value="{{ $variants->first() ?? 'Standard' }}">
                                    @endif
                                    {{-- ✅ FIN DE LA CORRECTION --}}

                                    <div class="quantity-input">
                                        <label for="quantity">Quantité :</label>
                                        <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock_quantity }}" required>
                                    </div>
                                    
                                    <button type="submit" class="add-to-cart-btn">
                                        🛒 Ajouter au panier
                                    </button>
                                </form>
                            @else
                                <button class="add-to-cart-btn disabled" disabled>Indisponible</button>
                            @endif
                        </div>
                    </div> {{-- /product-info --}}

                </div> {{-- /product-container --}}
            </main>
        </div>
    </div>

    {{-- SCRIPT D'INITIALISATION DE SWIPER --}}
    <script>
        if (typeof Swiper !== 'undefined') {
            
            // 1. Initialiser le Slider Miniature (Thumbnails)
            const thumbnailsSlider = new Swiper('#thumbnails-slider', {
                spaceBetween: 10,
                slidesPerView: 4,
                freeMode: true,
                watchSlidesProgress: true,
            });

            // 2. Initialiser le Slider Principal et le lier aux miniatures
            const mainSlider = new Swiper('#main-slider', {
                spaceBetween: 10,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                thumbs: {
                    swiper: thumbnailsSlider, // Lien vers le slider miniature
                },
            });
        }
    </script>

</body>
</html>