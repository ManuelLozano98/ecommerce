<?php

namespace Tests\Controller;

use App\Controllers\Controller;
use App\Services\CategoryService;
use App\Services\ProductService;
use App\Services\ReviewService;
use App\Services\UserService;
use App\Services\ProductImageService;
use App\Services\ProductInformationService;
use App\Services\ShippingAddressService;
use PHPUnit\Framework\TestCase;
use Slim\Views\PhpRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ControllerTest extends TestCase
{
    private Controller $controller;

    private $renderer;
    private $categoryService;
    private $productService;
    private $reviewService;
    private $userService;
    private $productImageService;
    private $productInformationService;
    private $shippingAddressService;

    protected function setUp(): void
    {

        // if (!defined('ROOT')) {
        //     define('ROOT', '/test');
        // }

        $this->renderer = $this->createMock(PhpRenderer::class);
        $this->categoryService = $this->createMock(CategoryService::class);
        $this->productService = $this->createMock(ProductService::class);
        $this->reviewService = $this->createMock(ReviewService::class);
        $this->userService = $this->createMock(UserService::class);
        $this->productImageService = $this->createMock(ProductImageService::class);
        $this->productInformationService = $this->createMock(ProductInformationService::class);
        $this->shippingAddressService = $this->createMock(ShippingAddressService::class);

        $this->controller = new Controller(
            $this->renderer,
            $this->categoryService,
            $this->productService,
            $this->reviewService,
            $this->userService,
            $this->productImageService,
            $this->productInformationService,
            $this->shippingAddressService
        );
    }

    public function testViewCartRendersCartTemplate(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $cart = [
            ['product_id' => 1, 'qty' => 2]
        ];

        $request->method('getAttribute')
            ->with('cart')
            ->willReturn($cart);

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with(
                $response,
                'cart.php',
                ['cart' => $cart]
            )
            ->willReturn($response);

        $result = $this->controller->viewCart(
            $request,
            $response,
            []
        );

        $this->assertSame($response, $result);
    }

    public function testIndexCheckoutRendersCheckoutForm(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with(
                $response,
                'checkout-form.php'
            )
            ->willReturn($response);

        $result = $this->controller->indexCheckout(
            $request,
            $response,
            []
        );

        $this->assertSame($response, $result);
    }

    public function testViewCategoryProductsReturns404WhenCategoryNotExists(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $this->categoryService
            ->expects($this->once())
            ->method('getCategoryBySlug')
            ->with('invalid-category')
            ->willReturn(null);

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with(
                $response,
                '404.php'
            )
            ->willReturn($response);

        $result = $this->controller->viewCategoryProducts(
            $request,
            $response,
            ['category' => 'invalid-category']
        );

        $this->assertSame($response, $result);
    }

    public function testViewProductReturns404WhenCategoryNotExists(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $this->categoryService
            ->expects($this->once())
            ->method('getCategoryBySlug')
            ->willReturn(null);

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with(
                $response,
                '404.php'
            )
            ->willReturn($response);

        $result = $this->controller->viewProduct(
            $request,
            $response,
            [
                'category' => 'test',
                'slug' => 'product'
            ]
        );

        $this->assertSame($response, $result);
    }

    public function testCheckoutStartReturnsErrorWhenBodyIsEmpty(): void
    {
        $stream = $this->createMock(\Psr\Http\Message\StreamInterface::class);
        $responseStream = $this->createMock(\Psr\Http\Message\StreamInterface::class);

        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $request->method('getBody')->willReturn($stream);
        $stream->method('getContents')->willReturn('');

        $response->method('getBody')->willReturn($responseStream);

        $responseStream
            ->expects($this->once())
            ->method('write')
            ->with(
                json_encode([
                    'success' => false,
                    'message' => 'No data',
                ])
            );

        $this->controller->checkoutStart(
            $request,
            $response,
            []
        );
    }

    public function testCheckoutAddressReturnsErrorWhenBodyIsEmpty(): void
    {
        $stream = $this->createMock(\Psr\Http\Message\StreamInterface::class);
        $responseStream = $this->createMock(\Psr\Http\Message\StreamInterface::class);

        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $request->method('getBody')->willReturn($stream);
        $stream->method('getContents')->willReturn('');

        $response->method('getBody')->willReturn($responseStream);

        $responseStream
            ->expects($this->once())
            ->method('write')
            ->with(
                json_encode([
                    'success' => false,
                    'message' => 'No data',
                ])
            );

        $this->controller->checkoutAddress(
            $request,
            $response,
            []
        );
    }

    public function testCheckoutStartRedirectsToLoginWhenUserNotLogged(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        $_SESSION = [];

        $payload = json_encode([
            'products' => [
                ['id' => 1, 'qty' => 2]
            ]
        ]);

        $stream = $this->createMock(\Psr\Http\Message\StreamInterface::class);
        $responseStream = $this->createMock(\Psr\Http\Message\StreamInterface::class);

        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $request->method('getBody')->willReturn($stream);
        $stream->method('getContents')->willReturn($payload);

        $response->method('getBody')->willReturn($responseStream);

        $responseStream
            ->expects($this->once())
            ->method('write')
            ->with($this->stringContains('"redirect"'));

        $this->controller->checkoutStart($request, $response, []);
    }


    public function testCheckoutStartReturnsSuccessWhenUserLogged(): void
    {
        $user = new class {
            public function getId(): int
            {
                return 1;
            }
        };

        $_SESSION['user'] = [
            'data' => $user
        ];

        $payload = json_encode([
            'products' => [
                ['id' => 1]
            ]
        ]);

        $stream = $this->createMock(\Psr\Http\Message\StreamInterface::class);
        $responseStream = $this->createMock(\Psr\Http\Message\StreamInterface::class);

        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $request->method('getBody')->willReturn($stream);
        $stream->method('getContents')->willReturn($payload);

        $response->method('getBody')->willReturn($responseStream);

        $responseStream
            ->expects($this->once())
            ->method('write')
            ->with($this->stringContains('"success":true'));

        $this->controller->checkoutStart($request, $response, []);
    }


    public function testCheckoutAddressReturnsValidationErrors(): void
    {
        $_SESSION['user'] = [
            'data' => new class {
                public function getId(): int
                {
                    return 1;
                }
            }
        ];

        $payload = json_encode([
            'full_name' => '',
            'phone' => 'abc',
            'address' => '',
            'city' => '',
            'province' => '',
            'postal_code' => '12'
        ]);

        $stream = $this->createMock(\Psr\Http\Message\StreamInterface::class);
        $responseStream = $this->createMock(\Psr\Http\Message\StreamInterface::class);

        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $request->method('getBody')->willReturn($stream);
        $stream->method('getContents')->willReturn($payload);

        $response->method('getBody')->willReturn($responseStream);

        $responseStream
            ->expects($this->once())
            ->method('write')
            ->with($this->stringContains('"Invalid form"'));

        $this->controller->checkoutAddress($request, $response, []);
    }


    public function testCheckoutAddressCreatesAddress(): void
    {
        $_SESSION['user'] = [
            'data' => new class {
                public function getId(): int
                {
                    return 1;
                }
            }
        ];

        $payload = json_encode([
            'full_name' => 'John Doe',
            'phone' => '600123456',
            'address' => 'Street 1',
            'city' => 'Madrid',
            'province' => 'Madrid',
            'postal_code' => '28001'
        ]);

        $stream = $this->createMock(\Psr\Http\Message\StreamInterface::class);
        $responseStream = $this->createMock(\Psr\Http\Message\StreamInterface::class);

        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $request->method('getBody')->willReturn($stream);
        $stream->method('getContents')->willReturn($payload);

        $response->method('getBody')->willReturn($responseStream);

        $this->shippingAddressService
            ->expects($this->once())
            ->method('getByUser')
            ->with(1)
            ->willReturn(null);

        $this->shippingAddressService
            ->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $responseStream
            ->expects($this->once())
            ->method('write')
            ->with($this->stringContains('"success":true'));

        $this->controller->checkoutAddress($request, $response, []);
    }

    public function testCheckoutAddressUpdatesAddress(): void
    {
        $_SESSION['user'] = [
            'data' => new class {
                public function getId(): int
                {
                    return 1;
                }
            }
        ];

        $payload = json_encode([
            'full_name' => 'John Doe',
            'phone' => '600123456',
            'address' => 'Street 1',
            'city' => 'Madrid',
            'province' => 'Madrid',
            'postal_code' => '28001'
        ]);

        $stream = $this->createMock(\Psr\Http\Message\StreamInterface::class);
        $responseStream = $this->createMock(\Psr\Http\Message\StreamInterface::class);

        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $request->method('getBody')->willReturn($stream);
        $stream->method('getContents')->willReturn($payload);

        $response->method('getBody')->willReturn($responseStream);

        $this->shippingAddressService
            ->expects($this->once())
            ->method('getByUser')
            ->with(1)
            ->willReturn(new \stdClass());

        $this->shippingAddressService
            ->expects($this->once())
            ->method('update')
            ->willReturn(true);

        $responseStream
            ->expects($this->once())
            ->method('write')
            ->with($this->stringContains('"success":true'));

        $this->controller->checkoutAddress($request, $response, []);
    }

    public function testViewProductRenders404WhenCategoryDoesNotExist(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $this->categoryService
            ->method('getCategoryBySlug')
            ->willReturn(null);

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with($response, '404.php')
            ->willReturn($response);

        $this->controller->viewProduct(
            $request,
            $response,
            [
                'category' => 'invalid',
                'slug' => 'product'
            ]
        );
    }

    public function testViewProductRenders404WhenProductDoesNotExist(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $category = $this->createMock(\stdClass::class);

        $this->categoryService
            ->method('getCategoryBySlug')
            ->willReturn($category);

        $this->productService
            ->method('getProductBySlug')
            ->willReturn(null);

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with($response, '404.php')
            ->willReturn($response);

        $this->controller->viewProduct(
            $request,
            $response,
            [
                'category' => 'electronics',
                'slug' => 'product'
            ]
        );
    }

    public function testIndexCheckoutAddressRendersView(): void
    {
        $user = new class {
            public function getId(): int
            {
                return 1;
            }
        };

        $_SESSION['user'] = [
            'data' => $user
        ];

        $_SESSION['checkout'] = [
            ['id' => 1]
        ];

        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $address = new \stdClass();
        $userData = new \stdClass();

        $this->shippingAddressService
            ->expects($this->once())
            ->method('getByUser')
            ->with(1)
            ->willReturn($address);

        $this->userService
            ->expects($this->once())
            ->method('getUser')
            ->with(1)
            ->willReturn($userData);

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with(
                $response,
                'checkout-address.php',
                [
                    'address' => $address,
                    'userData' => $userData,
                    'products' => $_SESSION['checkout']
                ]
            )
            ->willReturn($response);

        $this->controller->indexCheckoutAddress(
            $request,
            $response,
            []
        );
    }
}
