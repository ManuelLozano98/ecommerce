<?php

namespace App\Middleware;

use App\Services\PasswordResetService;
use Slim\Routing\RouteContext;

class TokenMiddleware
{
    private PasswordResetService $passwordService;

    public function __construct(PasswordResetService $passwordService)
    {
        $this->passwordService = $passwordService;
    }

    public function __invoke($request, $handler)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $args = $route->getArguments();
        $token = $args['token'];
        $password = $this->passwordService->getByToken($token);
        if (!$password || $password?->isUsed()) {
            $response = new \Slim\Psr7\Response();
            return $response
                ->withHeader('Location',  ROOT . "/forgot-password?error=invalid-link")
                ->withStatus(302);
        }

        return $handler->handle($request);
    }
}
