<?php

namespace App\Handlers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpMethodNotAllowedException;
use Throwable;

use App\Exceptions\NotFoundException;
use App\Exceptions\DuplicateException;
use App\Exceptions\ForeignKeyException;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;

class ErrorHandler
{
    public static function handle(
        Request $request,
        Throwable $exception,
        bool $displayErrorDetails
    ): Response {

        $responseFactory = new ResponseFactory();
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

        if ($exception instanceof DuplicateException || $exception instanceof ForeignKeyException) {
            $status = 409;
            $message = $exception->getMessage();
        }

        if ($exception instanceof InsertException || $exception instanceof UpdateException || $exception instanceof DeleteException) {
            $status = 400;
            $message = $exception->getMessage();
        }

        // API
        if ($isApi) {
            $response->getBody()->write(json_encode(['error' => $message]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($status);
        }

        // WEB
        if ($status === 404) {
            ob_start();
            require __DIR__ . '/../views/404.php';
            $html = ob_get_clean();
            $response->getBody()->write($html);
            return $response->withStatus(404);
        }

        ob_start();
        require __DIR__ . '/../views/500.html';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response->withStatus(500);
    }
}
