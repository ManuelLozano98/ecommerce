<?php

namespace App\Middleware;

class AdminMiddleware
{

    public function __invoke($request, $handler)
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user || empty($user['roles']) || !in_array('admin', $user['roles'], true)) {
            $response = new \Slim\Psr7\Response();
            ob_start();
            require __DIR__ . '/../views/404.php';
            $html = ob_get_clean();
            $response->getBody()->write($html);
            return $response->withStatus(404);
        }

        return $handler->handle($request);
    }
}
