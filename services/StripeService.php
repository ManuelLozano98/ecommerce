<?php

namespace App\Services;

use Stripe\StripeClient;
use App\Config\Env;
use Stripe\Webhook;

class StripeService
{
    private StripeClient $stripeClient;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    public function createCheckout(array $products, int $userId)
    {
        $checkoutType = $products[0]['checkout_type'];
        $lineItems = [];

        foreach ($products as $product) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $product["name"],
                        'description' => $product["description"],
                        'images' => [URL_LOCAL_SITE . "uploads/images/" . $product["image"]], //LOCAL IMAGES CANNOT BE RENDERED
                        'metadata' => [
                            'product_id' => (int)$product["id"]
                        ],
                    ],
                    'unit_amount' => (int) ($product["price"] * 100),
                ],
                'quantity' => $product["quantity"] ?? 1,
            ];
        }

        return $this->stripeClient->checkout->sessions->create([
            'ui_mode' => 'embedded_page',
            'line_items' => $lineItems,
            'mode' => 'payment',
            'metadata' => [
                'user_id' => $userId,
                'checkout_type' => $checkoutType
            ],
            'return_url' => URL_LOCAL_SITE . 'checkoutReturn?session_id={CHECKOUT_SESSION_ID}',
        ]);
    }

    public function createWebhook($request, $response, $args)
    {
        $payload = $request->getBody()->getContents();
        $sigHeader = $request->getHeaderLine('Stripe-Signature');
        $endpointSecret = Env::get("STRIPE_WEBHOOK");
        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            return $response->withStatus(400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return $response->withStatus(400);
        }
        return $event;
    }
}
