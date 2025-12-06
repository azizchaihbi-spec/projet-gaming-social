<?php
require_once __DIR__ . '/../vendor/stripe/stripe-php/init.php';
require_once __DIR__ . '/../config/stripe_config.php';

class PaymentController {
    
    public static function createStripeCheckout($montant, $donateur_nom, $donateur_email, $id_association, $association_nom) {
        try {
            \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
            
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Don pour ' . $association_nom,
                            'description' => 'Contribution à l\'association via Play to Help',
                        ],
                        'unit_amount' => $montant * 100, // Stripe utilise les centimes
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => BASE_URL . '/views/frontoffice/payment_success.php?session_id={CHECKOUT_SESSION_ID}&montant=' . $montant . '&nom=' . urlencode($donateur_nom) . '&email=' . urlencode($donateur_email) . '&id_association=' . $id_association,
                'cancel_url' => BASE_URL . '/views/frontoffice/don.php?cancelled=1',
                'customer_email' => $donateur_email,
                'metadata' => [
                    'donateur_nom' => $donateur_nom,
                    'id_association' => $id_association,
                    'association_nom' => $association_nom
                ]
            ]);
            
            return [
                'success' => true,
                'sessionId' => $session->id,
                'url' => $session->url
            ];
            
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public static function verifyPayment($sessionId) {
        try {
            \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
            
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            
            if ($session->payment_status === 'paid') {
                return [
                    'success' => true,
                    'session' => $session
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Paiement non confirmé'
                ];
            }
            
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
