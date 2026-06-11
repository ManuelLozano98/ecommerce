<?php

namespace Tests\Unit\Controllers;

use PHPUnit\Framework\TestCase;
use App\Controllers\StripeController;
use Stripe\StripeClient;
use App\Services\StripeService;
use App\Services\ProductService;
use App\Services\SaleService;
use App\Services\SaleItemService;
use App\Services\CartService;
use App\Services\MailService;
use Slim\Views\PhpRenderer;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class FakeStripeClient extends StripeClient
{
    public $checkout;

    public function __construct()
    {
        // Evita ejecutar el constructor real de StripeClient
    }
}

class StripeControllerTest extends TestCase
{
    private function makeResponse(): ResponseInterface
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('write')->willReturn(0);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($stream);
        $response->method('withHeader')->willReturnSelf();
        $response->method('withStatus')->willReturnSelf();

        return $response;
    }

    private function makeStripeClient(): StripeClient
    {
        return new FakeStripeClient();
    }

    private function makeController(StripeClient $stripeClient)
    {
        return new StripeController(
            $this->createMock(StripeService::class),
            $stripeClient,
            $this->createMock(ProductService::class),
            $this->createMock(SaleService::class),
            $this->createMock(SaleItemService::class),
            $this->createMock(CartService::class),
            $this->createMock(MailService::class),
            $this->createMock(PhpRenderer::class)
        );
    }

    public function testCheckoutNoSession()
    {
        $_SESSION['checkout'] = null;

        $controller = $this->makeController($this->makeStripeClient());

        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->makeResponse();

        $result = $controller->checkout($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testCheckoutWithStockError()
    {
        $_SESSION['checkout'] = [
            ['id' => 1, 'quantity' => 5, 'name' => 'Item']
        ];

        $product = new class {
            public function getStock()
            {
                return 1;
            }
        };

        $productService = $this->createMock(ProductService::class);
        $productService->method('getProduct')
            ->willReturn($product);

        $controller = new StripeController(
            $this->createMock(StripeService::class),
            $this->makeStripeClient(),
            $productService,
            $this->createMock(SaleService::class),
            $this->createMock(SaleItemService::class),
            $this->createMock(CartService::class),
            $this->createMock(MailService::class),
            $this->createMock(PhpRenderer::class)
        );

        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->makeResponse();

        $result = $controller->checkout($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testCheckCheckoutSuccess()
    {
        $session = (object)[
            'status' => 'complete',
            'customer_details' => (object)[
                'email' => 'test@test.com'
            ]
        ];

        $sessions = new class($session) {
            private $session;

            public function __construct($session)
            {
                $this->session = $session;
            }

            public function retrieve($id)
            {
                return $this->session;
            }
        };

        $stripe = $this->makeStripeClient();

        $stripe->checkout = new class($sessions) {
            public $sessions;

            public function __construct($sessions)
            {
                $this->sessions = $sessions;
            }
        };

        $_SESSION = [];

        $controller = $this->makeController($stripe);

        $request = $this->createMock(ServerRequestInterface::class);

        $body = $this->createMock(StreamInterface::class);
        $body->method('getContents')
            ->willReturn(json_encode([
                'session_id' => '123'
            ]));

        $request->method('getBody')->willReturn($body);

        $response = $this->makeResponse();

        $result = $controller->checkCheckout($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testIndexCheckoutReturnRedirect()
    {
        $session = (object)[
            'customer_details' => (object)[
                'email' => 'test@test.com'
            ]
        ];

        $stripe = $this->makeStripeClient();

        $stripe->checkout = new class($session) {
            public $sessions;

            public function __construct($session)
            {
                $this->sessions = new class($session) {
                    private $session;

                    public function __construct($session)
                    {
                        $this->session = $session;
                    }

                    public function retrieve($id)
                    {
                        return $this->session;
                    }
                };
            }
        };

        $renderer = $this->createMock(PhpRenderer::class);
        $renderer->method('render')->willReturnCallback(fn ($r) => $r);

        $controller = new StripeController(
            $this->createMock(StripeService::class),
            $stripe,
            $this->createMock(ProductService::class),
            $this->createMock(SaleService::class),
            $this->createMock(SaleItemService::class),
            $this->createMock(CartService::class),
            $this->createMock(MailService::class),
            $renderer
        );

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')
            ->willReturn(['session_id' => '123']);

        $response = $this->makeResponse();

        $result = $controller->indexCheckoutReturn($request, $response, []);

        $this->assertSame($response, $result);
    }
}