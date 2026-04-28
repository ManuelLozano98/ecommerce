<?php

namespace App\Middleware;

class AuthMiddleware
{
    public function __invoke($request, $handler)
    {
        if (!isset($_SESSION["user"])) {
            $response = new \Slim\Psr7\Response();
            return $response
                ->withHeader('Location',  ROOT)
                ->withStatus(302);
        }

        return $handler->handle($request);
    }
}
