<?php

namespace App\Middleware;

use App\Services\CartService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;

class CartMiddleware
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $cart = $this->cartService->getCartForCurrentUser();
        $request = $request->withAttribute('cart', $cart);
        return $handler->handle($request);
    }
}
