<?php

namespace Tests\Controllers;

use App\Controllers\CartController;
use App\Models\Cart;
use App\Models\Category;
use App\Services\CartService;
use App\Services\CategoryService;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Slim\Views\PhpRenderer;

class CartControllerTest extends TestCase
{
    private CartController $controller;

    private PhpRenderer $renderer;
    private CartService $cartService;
    private CategoryService $categoryService;

    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $_SESSION = [];

        $this->renderer = $this->createMock(PhpRenderer::class);
        $this->cartService = $this->createMock(CartService::class);
        $this->categoryService = $this->createMock(CategoryService::class);

        $this->controller = new CartController(
            $this->renderer,
            $this->cartService,
            $this->categoryService
        );
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    private function mockRequest(string $body): ServerRequestInterface
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $stream = $this->createMock(StreamInterface::class);

        $request->method('getBody')->willReturn($stream);
        $stream->method('getContents')->willReturn($body);

        return $request;
    }

    private function mockResponse(StreamInterface $stream): ResponseInterface
    {
        $response = $this->createMock(ResponseInterface::class);

        $response->method('getBody')->willReturn($stream);
        $response->method('withHeader')->willReturnSelf();
        $response->method('withStatus')->willReturnSelf();

        return $response;
    }

    public function testIndexRendersPage(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with($response, 'cart.php')
            ->willReturn($response);

        $result = $this->controller->index(
            $request,
            $response,
            []
        );

        $this->assertSame($response, $result);
    }



    public function testSetAndGetPageName(): void
    {
        $this->controller->setPageName('custom.php');

        $this->assertEquals(
            'custom.php',
            $this->controller->getPageName()
        );
    }

    public function testDeleteGuestCartItem(): void
    {
        $_SESSION['cart'] = [1 => new Cart([
            'product' => ['id' => 1],
            'quantity' => 1
        ])];

        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())
            ->method('write')
            ->with($this->stringContains('"success":true'));

        $response = $this->mockResponse($stream);

        $this->controller->delete(
            $this->createMock(ServerRequestInterface::class),
            $response,
            ['id' => 1]
        );

        $this->assertArrayNotHasKey(1, $_SESSION['cart']);
    }

    public function testDeleteUserCartItem(): void
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

        $this->cartService
            ->expects($this->once())
            ->method('deleteByUserAndProduct')
            ->with(1, 5)
            ->willReturn(true);

        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())
            ->method('write')
            ->with($this->stringContains('"success":true'));

        $response = $this->mockResponse($stream);

        $this->controller->delete(
            $this->createMock(ServerRequestInterface::class),
            $response,
            ['id' => 5]
        );
    }

    public function testDeleteReturnsErrorWhenProductCannotBeRemoved(): void
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

        $request = $this->createMock(ServerRequestInterface::class);

        $stream = $this->createMock(StreamInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $response
            ->method('getBody')
            ->willReturn($stream);

        $this->cartService
            ->expects($this->once())
            ->method('deleteByUserAndProduct')
            ->willReturn(false);

        $stream
            ->expects($this->once())
            ->method('write')
            ->with(
                $this->stringContains('"success":false')
            );

        $this->controller->delete(
            $request,
            $response,
            ['id' => 10]
        );
    }

    public function testAddReturnsErrorWhenBodyIsInvalid(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);

        $requestBody = $this->createMock(StreamInterface::class);
        $request->method('getBody')->willReturn($requestBody);

        $requestBody
            ->method('getContents')
            ->willReturn('');

        $stream = $this->createMock(StreamInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $response
            ->method('getBody')
            ->willReturn($stream);

        $response
            ->method('withHeader')
            ->willReturnSelf();

        $response
            ->method('withStatus')
            ->willReturnSelf();

        $stream
            ->expects($this->once())
            ->method('write')
            ->with(
                $this->stringContains('Invalid data')
            );

        $this->controller->add(
            $request,
            $response,
            []
        );
    }

    public function testAddCreatesCartItemForGuest(): void
    {
        $_SESSION = [];

        $payload = [
            'quantity' => 2,
            'product' => [
                'id' => 1,
                'name' => 'Product Test',
                'category_id' => 3
            ]
        ];

        $request = $this->createMock(ServerRequestInterface::class);

        $requestBody = $this->createMock(StreamInterface::class);
        $request->method('getBody')->willReturn($requestBody);
        $requestBody->method('getContents')->willReturn(json_encode($payload));

        $responseBody = $this->createMock(StreamInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($responseBody);
        $response->method('withHeader')->willReturnSelf();

        $category = $this->createMock(Category::class);

        $category
            ->method('getId')
            ->willReturn(3);

        $this->categoryService
            ->expects($this->once())
            ->method('getCategory')
            ->with(3)
            ->willReturn($category);

        $responseBody
            ->expects($this->once())
            ->method('write')
            ->with($this->stringContains('"success":true'));

        $this->controller->add($request, $response, []);

        $this->assertArrayHasKey(1, $_SESSION['cart']);
        $this->assertEquals(
            2,
            $_SESSION['cart'][1]->getQuantity()
        );
    }

    public function testAddIncrementsQuantityWhenProductAlreadyExists(): void
    {
        $_SESSION = [];

        $payload = [
            'quantity' => 2,
            'product' => [
                'id' => 1,
                'name' => 'Product Test',
                'category_id' => 3
            ]
        ];

        $_SESSION['cart'][1] = new Cart([
            'quantity' => 1,
            'product' => [
                'id' => 1,
                'name' => 'Product Test',
                'category_id' => 3
            ]
        ]);

        $request = $this->createMock(ServerRequestInterface::class);

        $requestBody = $this->createMock(StreamInterface::class);
        $request->method('getBody')->willReturn($requestBody);
        $requestBody->method('getContents')->willReturn(json_encode($payload));

        $responseBody = $this->createMock(StreamInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($responseBody);
        $response->method('withHeader')->willReturnSelf();

        $category = $this->createMock(Category::class);

        $category
            ->method('getId')
            ->willReturn(2);

        $this->categoryService
            ->method('getCategory')
            ->willReturn($category);

        $this->controller->add($request, $response, []);

        $this->assertEquals(
            3,
            $_SESSION['cart'][1]->getQuantity()
        );
    }

    public function testAddLoggedUserCreatesDatabaseCartItem(): void
    {
        $user = new class {
            public function getId(): int
            {
                return 99;
            }
        };

        $_SESSION['user'] = [
            'data' => $user
        ];

        $payload = [
            'quantity' => 1,
            'product' => [
                'id' => 10,
                'name' => 'Mouse',
                'category_id' => 2
            ]
        ];

        $request = $this->createMock(ServerRequestInterface::class);

        $requestBody = $this->createMock(StreamInterface::class);
        $request->method('getBody')->willReturn($requestBody);
        $requestBody->method('getContents')->willReturn(json_encode($payload));

        $responseBody = $this->createMock(StreamInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($responseBody);
        $response->method('withHeader')->willReturnSelf();

        $category = $this->createMock(Category::class);

        $category
            ->method('getId')
            ->willReturn(2);

        $this->categoryService
            ->method('getCategory')
            ->willReturn($category);

        $this->cartService
            ->expects($this->once())
            ->method('getCartByUser')
            ->with(99)
            ->willReturn([]);

        $this->cartService
            ->expects($this->once())
            ->method('save');

        $this->controller->add($request, $response, []);

        $this->assertArrayNotHasKey('cart', $_SESSION);
    }

    public function testAddLoggedUserUpdatesExistingCartItem(): void
    {
        $user = new class {
            public function getId(): int
            {
                return 55;
            }
        };

        $_SESSION['user'] = [
            'data' => $user
        ];

        $payload = [
            'quantity' => 2,
            'product' => [
                'id' => 20,
                'name' => 'Keyboard',
                'category_id' => 1
            ]
        ];

        $existingCart = new Cart([
            'quantity' => 1,
            'product' => [
                'id' => 20,
                'name' => 'Keyboard',
                'category_id' => 1
            ]
        ]);

        $request = $this->createMock(ServerRequestInterface::class);

        $requestBody = $this->createMock(StreamInterface::class);
        $request->method('getBody')->willReturn($requestBody);
        $requestBody->method('getContents')->willReturn(json_encode($payload));

        $responseBody = $this->createMock(StreamInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($responseBody);
        $response->method('withHeader')->willReturnSelf();

        $category = $this->createMock(Category::class);

        $category
            ->method('getId')
            ->willReturn(1);

        $this->categoryService
            ->method('getCategory')
            ->willReturn($category);

        $this->cartService
            ->expects($this->once())
            ->method('getCartByUser')
            ->with(55)
            ->willReturn([$existingCart]);

        $this->cartService
            ->expects($this->once())
            ->method('update')
            ->with(
                $this->callback(function ($cart) {
                    return $cart->getQuantity() === 3;
                })
            );

        $this->controller->add($request, $response, []);
    }
}
