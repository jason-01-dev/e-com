<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier | Boutique en Ligne</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    <header class="main-header cart-header">
        <a href="{{ route('front.index') }}" class="back-link">
            ← Continuer mes achats
        </a>
        <span class="cart-summary">
            Mon Panier (@if(isset($cart) && $cart->count())<span class="cart-count">{{ $cart->count() }}</span>@else 0 @endif)
        </span>
    </header>

    <div class="container cart-container">
        <h1>Votre Panier</h1>

        {{-- Messages flash --}}
        @if (session('success'))
            <div class="flash-success-public">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="flash-error-public">{{ session('error') }}</div>
        @endif
        
        {{-- Gestion des erreurs de validation (ex: quantité non valide) --}}
        @if ($errors->any())
            <div class="flash-error-public">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Panier vide --}}
        @if($cart->isEmpty())
            <div class="cart-empty-state">
                <p>Votre panier est vide pour le moment.</p>
                <a href="{{ route('front.index') }}" class="btn checkout-btn">
                    Retourner à la boutique
                </a>
            </div>
        @else
            <table class="cart-table">
                <thead>
                    <tr>
                        <th colspan="2">Produit</th>
                        <th>Prix Unitaire</th>
                        <th>Quantité</th>
                        <th>Total Ligne</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cart as $item)
                        <tr class="cart-item-row">
                            {{-- Image --}}
                            <td class="cart-item-image-cell">
                                <img src="{{ $item->attributes['image'] ?? 'https://via.placeholder.com/80x80?text=Image' }}" 
                                        alt="{{ $item->name }}" 
                                        class="cart-item-image">
                            </td>
                            {{-- Nom & Variante --}}
                            <td>
                                <a href="{{ route('front.product.show', $item->attributes['slug']) }}" class="item-name">
                                    **{{ $item->name }}**
                                </a>
                                {{-- Affichage de la variante si elle existe --}}
                                @if($item->attributes['variant'])
                                    <p class="item-variant">Variante: {{ $item->attributes['variant'] }}</p>
                                @endif
                            </td>
                            {{-- Prix Unitaire --}}
                            <td class="item-price">{{ number_format($item->price, 2, ',', ' ') }} €</td>
                            
                            {{-- Quantité et Mise à jour --}}
                            <td>
                                {{-- ⚠️ ATTENTION : $item->id est l'ID combiné (ex: '12-Rouge') --}}
                                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="update-form">
                                    @csrf
                                    @method('PATCH')
                                    
                                    <input type="number" 
                                            name="quantity" 
                                            value="{{ $item->quantity }}" 
                                            min="1" 
                                            class="cart-quantity-input">
                                    <button type="submit" class="btn btn-cart-update">
                                        Mettre à jour
                                    </button>
                                </form>
                            </td>

                            {{-- Total Ligne --}}
                            <td class="line-total">{{ number_format(\Cart::get($item->id)->getPriceSum(), 2, ',', ' ') }} €</td>

                            {{-- Suppression --}}
                            <td>
                                {{-- ⚠️ ATTENTION : $item->id est l'ID combiné (ex: '12-Rouge') --}}
                                <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-cart-remove" onclick="return confirm('Êtes-vous sûr de vouloir retirer cet article ?')">
                                        ❌ Retirer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="totals-checkout-area">
                <div class="totals">
                    {{-- 💡 UTILISATION DE $total fourni par le contrôleur --}}
                    <p class="grand-total-line">Total TTC : <span class="grand-total-amount">**{{ number_format($total, 2, ',', ' ') }} €**</span></p>
                </div>

                <a href="{{ route('checkout.index') }}" class="btn checkout-btn">
                    Procéder à la caisse (Checkout) →
                </a>

                {{-- Vider tout le panier --}}
                <form action="{{ route('cart.clear') }}" method="POST" class="clear-cart-form">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-clear-cart" onclick="return confirm('Voulez-vous vraiment vider tout le panier ?')">
                        Vider le panier 🗑️
                    </button>
                </form>
            </div>

            <p class="continue-shopping">
                <a href="{{ route('front.index') }}" class="back-link">
                    ← Continuer mes achats
                </a>
            </p>

        @endif
    </div>
</body>
</html>