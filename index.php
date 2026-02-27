<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/constants.php';

use Slim\Factory\AppFactory;
use App\Middleware\ApiMiddleware;
use Middlewares\TrailingSlash;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpMethodNotAllowedException;
use Psr\Http\Message\ServerRequestInterface;
use App\Exceptions\NotFoundException;
use App\Exceptions\DuplicateException;
use App\Exceptions\ForeignKeyException;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;

$app = AppFactory::create();

$app->setBasePath('/Ecommerce'); // Set the base path to your project folder

$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$errorMiddleware->setDefaultErrorHandler(
    function (
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails
    ) use ($app) {

        $responseFactory = $app->getResponseFactory();
        $response = $responseFactory->createResponse();

        $path = $request->getUri()->getPath();
        $isApi = str_contains($path, '/api/');

        $status = 500;
        $message = 'Internal Server Error';

        // HTTP ERRORS
        if ($exception instanceof HttpNotFoundException) {
            $status = 404;
            $message = 'Route not found';
        }

        if ($exception instanceof HttpMethodNotAllowedException) {
            $status = 405;
            $message = 'Method not allowed. Must be one of: ' . implode(', ', $exception->getAllowedMethods());
        }

        // SERVICE ERRORS
        if ($exception instanceof NotFoundException) {
            $status = 404;
            $message = $exception->getMessage();
        }

        if (
            $exception instanceof DuplicateException ||
            $exception instanceof ForeignKeyException
        ) {
            $status = 409;
            $message = $exception->getMessage();
        }

        if (
            $exception instanceof InsertException ||
            $exception instanceof UpdateException ||
            $exception instanceof DeleteException
        ) {
            $status = 400;
            $message = $exception->getMessage();
        }

        // API
        if ($isApi) {
            $response->getBody()->write(json_encode([
                'error' => $message
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($status);
        }

        // WEB
        if ($status === 404) {
            ob_start();
            require __DIR__ . '/views/404.php';
            $html = ob_get_clean();

            $response->getBody()->write($html);
            return $response->withStatus(404);
        } else {
            ob_start();
            require __DIR__ . '/views/500.html';
            $html = ob_get_clean();
            $response->getBody()->write($html);
            return $response->withStatus(500);
        }

        return $response
            ->withHeader('Content-Type', 'text/html')
            ->withStatus($status);
    }
);

$app->group('/api', function ($group) {
    (require __DIR__ . '/routes/api.php')($group);
})->add(new ApiMiddleware($app->getResponseFactory()));

$app->add(new TrailingSlash(true)); // Prevent issues if user forgets the trailing slash by adding it

(require __DIR__ . '/routes/web.php')($app);

$app->run();
