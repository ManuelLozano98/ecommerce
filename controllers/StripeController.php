<?php

namespace App\Controllers;

use App\Models\ShippingAddress;
use App\Services\StripeService;
use Slim\Views\PhpRenderer;
use Stripe\StripeClient;
use App\Services\ProductService;
use Exception;
use App\Services\SaleService;
use App\Services\SaleItemService;
use App\Services\CartService;
use App\Services\MailService;
use App\Services\ShippingAddressService;
use App\Services\ProductInformationService;
use App\Services\ShipmentService;
use App\Services\ShippoService;
use Shippo_Shipment;
use Shippo_Transaction;
use Shippo_CustomsDeclaration;
use App\Utils\CountryCodeHelper;

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
    private ShippingAddressService $shippingAddressService;
    private ProductInformationService $productInformationService;
    private ShipmentService $shipmentService;
    private ShippoService $shippoService;

    public function __construct(StripeService $stripeService, StripeClient $stripeClient, ProductService $productService, SaleService $saleService, SaleItemService $saleItemService, CartService $cartService, MailService $mailService, PhpRenderer $renderer, ShippingAddressService $shippingAddressService, ProductInformationService $productInformationService, ShipmentService $shipmentService, ShippoService $shippoService)
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
        $this->shippingAddressService = $shippingAddressService;
        $this->productInformationService = $productInformationService;
        $this->shipmentService = $shipmentService;
        $this->shippoService = $shippoService;
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
                $productsDetails = [];
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

                    //RETRIEVE PRODUCT DEATILS
                    try {
                        $detail = $this->productInformationService->getById((int)$item->price->product->metadata->product_id);
                        array_push($productsDetails, $detail);
                    } catch (Exception $e) {
                        continue;
                    }
                }


                // REMOVE USER CART
                $type = $session->metadata->checkout_type;
                if ($type === 'cart') {
                    $this->cartService->deleteUserCart($session->metadata->user_id);
                }
                unset($_SESSION["checkout"]);


                // UPDATE COUNTRY
                $address = $this->shippingAddressService->getByUser($session->metadata->user_id);
                $country = $this->shippingAddressService->getCountryFromAddressOSM($address->getAddress(), $address->getCity(), $address->getProvince());
                $address->setCountry($country);
                $this->shipmentService->update($address->toArray());

                // CREATE A SHIPPING PROCESS
                [$transaction, $shipm, $rate]  = $this->createShippingProcess($address, $productsDetails, $session->customer_details->email, $items);
                $shipment = [
                    'sale_id' => $sale->getId(),
                    'shippo_shipment_id' => $shipm->object_id ?? null,
                    'shippo_transaction_id' => $transaction->object_id ?? null,
                    'stripe_session_id' => $session->id ?? null,
                    'tracking_number' => $transaction->tracking_number ?? null,
                    'status' => $transaction->status ?? null,
                    'label_url' => $transaction->label_url ?? null,
                    'carrier' => $rate->provider,

                ];
                $this->shipmentService->save($shipment);


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

    public function createShippingProcess(ShippingAddress $address, array $details, string $email, \Stripe\Collection $items)
    {
        $parcels = [];

        // Example from_address array
        $from_address = array(
            'name' => 'Mr Hippo',
            'company' => 'Shippo',
            'street1' => '215 Clayton St.',
            'city' => 'San Francisco',
            'state' => 'CA',
            'zip' => '94117',
            'country' => 'US',
            'phone' => '+1 555 341 9393',
            'email' => 'mr-hippo@goshipppo.com',
        );
        $country = strtoupper(CountryCodeHelper::getCountryCode($address->getCountry()));
        $to_address = array(
            'name' => $address->getFullName(),
            'street1' => $address->getAddress(),
            'city' => $address->getCity(),
            'state' => $address->getProvince(),
            'zip' => $address->getPostalCode(),
            'country' => $country = $country ?: 'ES',
            'phone' => $address->getPhone(),
            'email' => $email,
        );

        // Example parcel array
        $parcel = array(
            'length' => 5,
            'width' => 5,
            'height' => 5,
            'distance_unit' => 'cm',
            'weight' => 100,
            'mass_unit' => 'g',
        );

        foreach ($details as $detail) {
            $weight = $detail->getWeight();

            // Extract the numeric weight value and unit (kg or g) from the input string
            preg_match('/^(\d+(?:\.\d+)?)(kg|g)$/i', $weight, $matches);

            $value = (float) $matches[1];
            $unit = strtolower($matches[2]);

            $grams = $unit === 'kg'
                ? $value * 1000
                : $value;

            /* Parse dimensions in the format "length x width x height"
             and capture each numeric value separately. */
            preg_match(
                '/([\d.]+)\s*x\s*([\d.]+)\s*x\s*([\d.]+)/i',
                $detail->getDimensions(),
                $matches
            );
            $parcels[] = [
                'length' => (float) $matches[1] == 0 ? $parcel['length'] : $matches[1],
                'width' => (float) $matches[2] == 0 ? $parcel['width'] : $matches[2],
                'height' => (float) $matches[3] == 0 ? $parcel['height'] : $matches[3],
                'distance_unit' => 'cm',
                'weight' => (float) $detail->getWeight() == 0 ? $parcel['weight'] : $grams,
                'mass_unit' => $unit,
            ];
        };


        if ($from_address['country'] !== $to_address['country']) {
            //INTERNATIONAL SHIPMENTS

            $data = [];
            foreach ($items->data as $index => $item) {
                $detail = $details[$index] ?? null;

                $data[] = [
                    'description' => $item->description ?? 'Product',

                    'quantity' => $item->quantity ?? 1,

                    'net_weight' => $parcels[$index]['weight'] ?? $parcel['weight'],
                    'mass_unit' => $parcels[$index]['mass_unit'],

                    'value_amount' => $item->amount_total
                        ? $item->amount_total / 100
                        : 0,

                    'value_currency' => $item->currency ?? 'EUR',

                    'origin_country' => $from_address['country'],
                ];
            }

            $customsDeclaration = Shippo_CustomsDeclaration::create([
                'certify' => true,
                'certify_signer' => 'John Doe',
                'contents_type' => 'MERCHANDISE',
                'non_delivery_option' => 'RETURN',
                'eel_pfc' => 'NOEEI_30_37_a',
                'items' => $data,
            ]);

            $shipment = Shippo_Shipment::create([
                'address_from' => $from_address,
                'address_to' => $to_address,
                'parcels' => $parcels,

                'customs_declaration' => $customsDeclaration->object_id
            ]);
        } else {
            $shipment = Shippo_Shipment::create(
                array(
                    'address_from' => $from_address,
                    'address_to' => $to_address,
                    'parcels' => count($parcels) <= 0 ? $parcel : $parcels,
                    'mass_unit' => 'kg'
                ),
                $this->shippoService->get()
            );
        }


        $rate = $shipment->rates[0];

        $transaction = Shippo_Transaction::create(
            array(
                'rate' => $rate->object_id,
                'async' => false,
            ),
            $this->shippoService->get()
        );

        return [$transaction, $shipment, $rate];
    }

    public function indexCheckoutReturn($request, $response, $args)
    {
        $params = $request->getQueryParams();
        $sessionId = $params['session_id'] ?? null;

        if (!$sessionId) {
            return $response->withHeader('Location', ROOT)->withStatus(302);
        }
        $shipment = $this->shipmentService->getByStripeId($sessionId);
        $tracking_number = $shipment->getTrackingNumber();

        try {
            $session = $this->stripeClient->checkout->sessions->retrieve($sessionId);
            $customer_email = $session->customer_details->email;
            return $this->renderer->render($response, "checkout-return.php", ['session' => $session, 'customer_email' => $customer_email, 'tracking_number' => $tracking_number]);
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
