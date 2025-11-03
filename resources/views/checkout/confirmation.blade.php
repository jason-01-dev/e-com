<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation de Commande n°{{ $order->id ?? '...' }}</title>
    
    {{-- ✅ Correction : Utilisation de la directive @vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js']) 
</head>
<body>

    <div class="container">
        
        <div class="confirmation-box">
            <h1>🎉 Commande Confirmée avec Succès !</h1>
            
            {{-- Le message de succès du contrôleur --}}
            @if (session('success'))
                <div class="flash-success-public">{{ session('success') }}</div>
            @endif

            <p class="thank-you-message">
                Merci, **{{ $order->customer_first_name ?? 'Client' }}**, pour votre achat.
            </p>
            
            <p class="order-id-display">
                Votre **numéro de commande** est : **#{{ $order->id ?? 'N/A' }}**
            </p>

            <p>Un e-mail de confirmation détaillé vous a été envoyé à l'adresse **{{ $order->customer_email ?? '...' }}**.</p>
            
            <hr>

            <div class="confirmation-details-row">
                
                {{-- 1. Récapitulatif de la commande --}}
                <div class="confirmation-details-card">
                    <h2>Détails de la Commande ({{ number_format($order->total_amount ?? 0, 2) }} €)</h2>
                    <ul class="summary-list">
                        @forelse ($order->items as $item)
                            <li>
                                <span>{{ $item->product_name }} (x{{ $item->quantity }})</span>
                                <span class="price-value">{{ number_format($item->price_at_sale * $item->quantity, 2) }} €</span>
                            </li>
                        @empty
                            <li><span class="error-message">Aucun article trouvé.</span></li>
                        @endforelse
                    </ul>
                    <div class="total-line">
                        <span>Total Payé</span>
                        <span class="grand-total-amount">{{ number_format($order->total_amount ?? 0, 2) }} €</span>
                    </div>
                </div>

                {{-- 2. Adresse de Livraison --}}
                <div class="confirmation-details-card">
                    <h2>Adresse de Livraison</h2>
                    <address>
                        {{ $order->customer_first_name ?? '' }} {{ $order->customer_last_name ?? '' }}<br>
                        {{ $order->shipping_address ?? '...' }}<br>
                        {{ $order->shipping_zip_code ?? '' }} {{ $order->shipping_city ?? '...' }}<br>
                        {{ $order->shipping_country ?? '' }}
                    </address>
                </div>

            </div>

            <a href="{{ route('front.index') }}" class="btn-primary home-link large-btn">
                ← Continuer mes achats
            </a>
            
            <p class="small-text">Vous pouvez vérifier le statut de votre commande dans votre espace client.</p>
        </div>
    </div>

</body>
</html>