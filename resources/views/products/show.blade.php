<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $product->name }} | Détail</title>
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
                    <li><a href="{{ route('front.index') }}">Toutes les Catégories</a></li> 
                    @foreach ($categories as $cat)
                        <li><a href="{{ route('front.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>
        </aside>

        <main class="product-detail">

            <h1>{{ $product->name }}</h1>
            <a href="{{ route('front.index') }}" class="back-link">← Retour au Catalogue</a>

            <div class="product-container">
                
                <div class="product-gallery">
                    
                    <div id="main-slider" class="swiper main-slider">
                        <div class="swiper-wrapper">
                            @foreach ($product->images->sortBy('id') as $image)
                                <div class="swiper-slide" data-variant="{{ $image->variant_name }}">
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

                    @if($product->images->count() > 1)
                        <div id="thumbnails-slider" class="swiper thumbnails-slider">
                            <div class="swiper-wrapper">
                                @foreach ($product->images->sortBy('id') as $image)
                                    <div class="swiper-slide" data-variant="{{ $image->variant_name }}">
                                        <img src="{{ Storage::url($image->path) }}" alt="Thumbnail {{ $loop->index + 1 }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                <div class="product-info">
                    
                    <p class="category-info">
                        Catégorie : @if ($product->category)
                            <a href="{{ route('front.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a>
                        @else
                            Non classé
                        @endif
                    </p>

                    <div class="price">{{ number_format($product->price, 2, ',', ' ') }} €</div>

                    <p class="{{ $product->stock_quantity > 0 ? 'stock-in' : 'stock-out' }}">
                        {{ $product->stock_quantity > 0 ? '✅ En Stock ('.$product->stock_quantity.' restants)' : '❌ Rupture de Stock' }}
                    </p>

                    <h2>Description</h2>
                    <p>{{ $product->description }}</p>

                    <div class="cart-form-wrapper">
                        @if ($product->stock_quantity > 0)
                            <form action="{{ route('cart.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">

                                @php
                                    $variants = $product->images->pluck('variant_name')->filter()->unique();
                                    $sizes = $product->available_sizes ? explode(',', $product->available_sizes) : [];
                                @endphp

                                {{-- Variante / couleur --}}
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
                                    <input type="hidden" name="variant_name" value="{{ $variants->first() ?? 'Standard' }}">
                                @endif

                                {{-- Taille --}}
                                @if(count($sizes) > 0)
                                    <div class="size-select">
                                        <label for="size">Taille :</label>
                                        <select name="size" id="size" required>
                                            @foreach($sizes as $size)
                                                <option value="{{ $size }}">{{ $size }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <input type="hidden" name="size" value="Standard">
                                @endif

                                <div class="quantity-input">
                                    <label for="quantity">Quantité :</label>
                                    <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock_quantity }}" required>
                                </div>

                                <button type="submit" class="add-to-cart-btn">🛒 Ajouter au panier</button>
                            </form>
                        @else
                            <button class="add-to-cart-btn disabled" disabled>Indisponible</button>
                        @endif
                    </div>
                </div>

            </div>
        </main>
    </div>
</div>

<script>
if (typeof Swiper !== 'undefined') {
    const thumbnailsSlider = new Swiper('#thumbnails-slider', {
        spaceBetween: 10,
        slidesPerView: 4,
        freeMode: true,
        watchSlidesProgress: true,
    });

    const mainSlider = new Swiper('#main-slider', {
        spaceBetween: 10,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        thumbs: {
            swiper: thumbnailsSlider,
        },
    });

    // Changer l'image principale en fonction de la variante sélectionnée
    const variantSelect = document.getElementById('variant_name');
    if(variantSelect){
        variantSelect.addEventListener('change', function(){
            const selectedVariant = this.value;
            const slides = document.querySelectorAll('#main-slider .swiper-slide');
            slides.forEach(slide => {
                slide.style.display = (slide.dataset.variant === selectedVariant) ? 'block' : 'none';
            });
            mainSlider.update(); // Met à jour Swiper pour afficher la bonne image
        });
    }
}
</script>

</body>
</html>
