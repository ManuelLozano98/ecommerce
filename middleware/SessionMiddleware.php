<?php

namespace App\Middleware;

class SessionMiddleware
{
    public function __invoke($request, $handler)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return $handler->handle($request);
    }
}
