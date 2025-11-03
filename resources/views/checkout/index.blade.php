<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Caisse (Checkout) | Boutique en Ligne</title>
    {{-- ✅ CORRECTION : Utilisation de la directive @vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    {{-- Utilisation de la classe CSS pour l'en-tête --}}
    <header class="main-header cart-header">
        <a href="{{ route('cart.index') }}" class="back-link">
            ← Retour au panier
        </a>
        <span class="cart-summary">
            Caisse : Récapitulatif
        </span>
    </header>

    <div class="container">
        <h1>Finaliser ma Commande</h1>

        <div class="checkout-container">
            
            <div class="form-section">
                <h2>1. Vos Informations et Adresse</h2>
                
                @if (session('error'))
                    <div class="flash-error-public">{{ session('error') }}</div>
                @endif

                <form action="{{ route('checkout.store') }}" method="POST">
                    @csrf
                    
                    {{-- Nom et Prénom (Séparés pour validation) --}}
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">Prénom <span style="color: red;">*</span></label>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                            @error('first_name')<p class="error-message">{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label for="last_name">Nom <span style="color: red;">*</span></label>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                            @error('last_name')<p class="error-message">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    
                    {{-- Email (Aligné sur le nom du contrôleur) --}}
                    <div class="form-group">
                        <label for="email">Adresse E-mail <span style="color: red;">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')<p class="error-message">{{ $message }}</p>@enderror
                    </div>
                    
                    {{-- Adresse --}}
                    <div class="form-group">
                        <label for="address">Adresse Complète <span style="color: red;">*</span></label>
                        <input type="text" id="address" name="address" value="{{ old('address') }}" required>
                        @error('address')<p class="error-message">{{ $message }}</p>@enderror
                    </div>

                    {{-- Ville et Code Postal (Aligné sur le nom du contrôleur) --}}
                    <div class="form-row"> 
                        <div class="form-group">
                            <label for="city">Ville <span style="color: red;">*</span></label>
                            <input type="text" id="city" name="city" value="{{ old('city') }}" required>
                            @error('city')<p class="error-message">{{ $message }}</p>@enderror
                        </div>
                        <div class="form-group">
                            <label for="zip_code">Code Postal <span style="color: red;">*</span></label>
                            <input type="text" id="zip_code" name="zip_code" value="{{ old('zip_code') }}" required>
                            @error('zip_code')<p class="error-message">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Pays --}}
                    <div class="form-group">
                        <label for="country">Pays</label>
                        {{-- NOTE: Le contrôleur ne valide pas 'country' pour l'instant, mais c'est une bonne pratique de l'inclure. --}}
                        <input type="text" id="country" name="country" value="{{ old('country', 'France') }}" required>
                        @error('country')<p class="error-message">{{ $message }}</p>@enderror
                    </div>

                    <div class="payment-section">
                        <h2>2. Méthode de Paiement</h2>
                        <p class="help-text">Paiement simulé uniquement pour l'enregistrement de la commande.</p>
                    </div>
                    
                    {{-- Bouton de soumission avec le total --}}
                    <button type="submit" class="checkout-submit-btn">
                        Payer et Confirmer la Commande ({{ number_format($totalGlobal, 2) }} €)
                    </button>
                </form>
            </div>

            {{-- Résumé de la commande --}}
            <div class="summary-section">
                <h2>Résumé de la Commande</h2>
                
                @foreach($cart as $item)
                    <div class="summary-item">
                        <span class="item-name">{{ $item['name'] }} (x{{ $item['quantity'] }})</span>
                        <span class="item-price">{{ number_format($item['price'] * $item['quantity'], 2) }} €</span>
                    </div>
                @endforeach
                
                {{-- Totaux (Utilisation de $totalGlobal) --}}
                <div class="summary-item subtotal">
                    <span>Sous-total des articles</span>
                    <span>{{ number_format($totalGlobal, 2) }} €</span> 
                </div>
                <div class="summary-item">
                    <span>Frais de Livraison</span>
                    <span>Gratuit</span>
                </div>
                
                <div class="summary-item grand-total">
                    <span>Total Final</span>
                    <span class="grand-total-amount">{{ number_format($totalGlobal, 2) }} €</span>
                </div>
                
                <p class="back-to-cart-link"><a href="{{ route('cart.index') }}">← Retourner au panier</a></p>
            </div>
        </div>
    </div>

</body>
</html>