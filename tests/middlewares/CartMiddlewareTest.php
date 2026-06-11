<?php

namespace Tests\Middleware;

use App\Middleware\CartMiddleware;
use App\Services\CartService;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CartMiddlewareTest extends TestCase
{
    private CartService $cartService;
    private CartMiddleware $middleware;

    protected function setUp(): void
    {
        $this->cartService = $this->createMock(CartService::class);

        $this->middleware = new CartMiddleware(
            $this->cartService
        );
    }

    public function testAddsCartToRequestAndCallsHandler(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);

        $modifiedRequest = $this->createMock(ServerRequestInterface::class);

        $this->cartService
            ->expects($this->once())
            ->method('getCartForCurrentUser')
            ->willReturn(['item-1', 'item-2']);

        $request
            ->expects($this->once())
            ->method('withAttribute')
            ->with(
                'cart',
                ['item-1', 'item-2']
            )
            ->willReturn($modifiedRequest);

        $expectedResponse = $this->createMock(ResponseInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);

        $handler
            ->expects($this->once())
            ->method('handle')
            ->with($modifiedRequest)
            ->willReturn($expectedResponse);

        $result = ($this->middleware)($request, $handler);

        $this->assertSame($expectedResponse, $result);
    }
}