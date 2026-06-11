<?php

namespace App\Controllers;

use App\Services\StripeService;
use Slim\Views\PhpRenderer;
use Stripe\StripeClient;
use App\Services\ProductService;
use Exception;
use App\Services\SaleService;
use App\Services\SaleItemService;
use App\Services\CartService;
use App\Services\MailService;

class StripeController
{
    private StripeService $stripeService;
    private StripeClient $stripeClient;
    private SaleService $saleService;
    private SaleItemService $saleItemService;
    private CartService $cartService;
    private ProductService $productService;
    private MailService $mailService;
    private PhpRenderer $renderer;

    public function __construct(StripeService $stripeService, StripeClient $stripeClient, ProductService $productService, SaleService $saleService, SaleItemService $saleItemService, CartService $cartService, MailService $mailService, PhpRenderer $renderer)
    {
        $this->stripeService = $stripeService;
        $this->stripeClient = $stripeClient;
        $this->productService = $productService;
        $this->saleService = $saleService;
        $this->saleItemService = $saleItemService;
        $this->cartService = $cartService;
        $this->productService = $productService;
        $this->mailService = $mailService;
        $this->renderer = $renderer;
    }
    public function checkout($request, $response, $args)
    {
        $data = $_SESSION['checkout'];

        if (!$data) {
            $response->getBody()->write(json_encode([
                'data' => null
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json');
        }

        //Validate stock
        $isStock = true;
        $msg = "";
        $details = [];
        foreach ($data as $item) {
            $stock = $this->productService->getProduct($item['id'])->getStock();
            $quantity = (int) $item['quantity'];
            if ($quantity > $stock) {
                $isStock = false;
                $msg = "Insufficient stock for ";
                $details[] = ['item' => [$item['name']]];
            }
        }
        if (!$isStock) {
            $response->getBody()->write(json_encode([
                'message' => $msg,
                'details' => $details
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        }

        //Create checkout
        $user = $_SESSION["user"] ?? NULL;
        $userId = 1;
        if ($user) {
            $userId = $user["data"]->getId();
        }
        $data = $this->stripeService->createCheckout($data, $userId);
        $response->getBody()->write(json_encode([
            'clientSecret' => $data->client_secret
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function checkCheckout($request, $response, $args)
    {
        try {
            $jsonObj = json_decode($request->getBody()->getContents());

            $session = $this->stripeClient->checkout->sessions->retrieve($jsonObj->session_id);

            $response->getBody()->write(json_encode([
                'status' => $session->status,
                'customer_email' => $session->customer_details->email
            ]));
            unset($_SESSION['cart']);

            return $response
                ->withHeader('Content-Type', 'application/json');
        } catch (\Error $e) {
            $response->getBody()->write(json_encode([
                'error' => $e->getMessage(),
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json');
        }
    }

    public function processOrderWebhook($request, $response, $args)
    {
        $event = $this->stripeService->createWebhook($request, $response, $args);
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;

                $paymentIntent = $this->stripeClient->paymentIntents->retrieve(
                    $session->payment_intent,
                    ['expand' => ['payment_method']]
                );

                $paymentMethod = $paymentIntent->payment_method;


                // CREATE ORDER
                $saleData = [
                    'user_id' => $session->metadata->user_id,
                    'total_amount' => $session->amount_total / 100,
                    'status' => $this->mapStatus($session->payment_status),
                    'payment_method' => $this->mapPayment($paymentMethod),
                    'created_at' =>  date('Y-m-d H:i:s', $session->created),
                ];
                $products = [];
                $sale = $this->saleService->save($saleData);
                $items = $this->stripeClient->checkout->sessions->allLineItems($session->id, ['expand' => ['data.price.product']]);
                foreach ($items->data as $item) {
                    $itemData = [
                        'sale_id' => $sale->getId(),
                        'product_id' => (int)$item->price->product->metadata->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price->unit_amount / 100,
                        'subtotal' => $item->amount_subtotal / 100,
                    ];
                    $this->saleItemService->save($itemData);

                    // DECREASE STOCK
                    $product = $this->productService->getProduct((int)$item->price->product->metadata->product_id);
                    $product->setStock($product->getStock() - $item->quantity);
                    $rawProduct = $product->toArray();
                    $this->productService->update($rawProduct);

                    //CREATE SALE ORDER FOR MAIL
                    $products[] = [
                        'name' => $product->getName(),
                        'image' => __DIR__ . '/../uploads/images/' . $product->getImage(),
                        'price' => $item->price->unit_amount / 100,
                        'quantity' => $item->quantity
                    ];
                }


                // REMOVE USER CART
                $type = $session->metadata->checkout_type;
                if ($type === 'cart') {
                    $this->cartService->deleteUserCart($session->metadata->user_id);
                }
                unset($_SESSION["checkout"]);

                // SEND EMAIL
                $email = $session->customer_details->email;
                $sale->setItems($products);
                $this->mailService->createOrderMailer($email, $sale);


                break;
            case 'checkout.session.expired':
                $session = $event->data->object;

                $paymentIntent = $this->stripeClient->paymentIntents->retrieve(
                    $session->payment_intent,
                    ['expand' => ['payment_method']]
                );

                $paymentMethod = $paymentIntent->payment_method;


                // CREATE ORDER
                $saleData = [
                    'user_id' => $session->metadata->user_id,
                    'total_amount' => $session->amount_total / 100,
                    'status' => 'canceled',
                    'payment_method' => $this->mapPayment($paymentMethod),
                    'created_at' =>  date('Y-m-d H:i:s', $session->created),
                ];
                $products = [];
                $sale = $this->saleService->save($saleData);
                $items = $this->stripeClient->checkout->sessions->allLineItems($session->id, ['expand' => ['data.price.product']]);
                foreach ($items->data as $item) {
                    $itemData = [
                        'sale_id' => $sale->getId(),
                        'product_id' => (int)$item->price->product->metadata->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price->unit_amount / 100,
                        'subtotal' => $item->amount_subtotal / 100,
                    ];
                    $this->saleItemService->save($itemData);
                }

                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $paymentIntent = $this->stripeClient->paymentIntents->retrieve(
                    $event->data->object->id,
                    ['expand' => ['payment_method']]
                );
                $paymentMethod = $paymentIntent->payment_method;

                $sessions = $this->stripeClient->checkout->sessions->all([
                    'payment_intent' => $paymentIntent->id,
                    'limit' => 1
                ]);
                $session = $sessions->data[0] ?? null;



                // CREATE ORDER
                $saleData = [
                    'user_id' => $paymentIntent->metadata->user_id,
                    'total_amount' => $paymentIntent->amount / 100,
                    'status' => $this->mapStatus($paymentIntent->status),
                    'payment_method' => $this->mapPayment($paymentMethod),
                    'created_at' =>  date('Y-m-d H:i:s', $paymentIntent->created),
                ];
                $products = [];
                $sale = $this->saleService->save($saleData);
                $items = $this->stripeClient->checkout->sessions->allLineItems($session->id, ['expand' => ['data.price.product']]);
                foreach ($items->data as $item) {
                    $itemData = [
                        'sale_id' => $sale->getId(),
                        'product_id' => (int)$item->price->product->metadata->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price->unit_amount / 100,
                        'subtotal' => $item->amount_subtotal / 100,
                    ];
                    $this->saleItemService->save($itemData);
                }
                break;
        }


        $response->getBody()->write('OK');
        return $response->withStatus(200);
    }

    public function indexCheckoutReturn($request, $response, $args)
    {
        $params = $request->getQueryParams();
        $sessionId = $params['session_id'] ?? null;

        if (!$sessionId) {
            return $response->withHeader('Location', ROOT)->withStatus(302);
        }
        try {
            $session = $this->stripeClient->checkout->sessions->retrieve($sessionId);
            $customer_email = $session->customer_details->email;
            return $this->renderer->render($response, "checkout-return.php", ['session' => $session, 'customer_email' => $customer_email]);
        } catch (\Stripe\Exception\InvalidRequestException) {
            return $response->withHeader('Location', ROOT)->withStatus(302);
        }
    }

    private function mapPayment($paymentMethod)
    {
        switch ($paymentMethod->type) {

            case 'card':

                $wallet = $paymentMethod->card->wallet->type ?? null;

                if ($wallet === 'apple_pay') {
                    return 'apple_pay';
                }

                if ($wallet === 'google_pay') {
                    return 'google_pay';
                }

                $funding = $paymentMethod->card->funding;

                return $funding === 'debit'
                    ? 'debit_card'
                    : 'credit_card';

            case 'paypal':
                return 'paypal';

            default:
                return 'credit_card';
        }
    }
    private function mapStatus($status)
    {
        if ($status === "paid") {
            return "completed";
        } else if ($status === "unpaid" || $status  === "processing") {
            return "pending";
        }
        return "failed";
    }
}
