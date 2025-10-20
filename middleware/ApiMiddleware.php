<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpException;

class ApiMiddleware implements MiddlewareInterface
{
    private ResponseFactoryInterface $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (HttpNotFoundException $e) {
            return $this->errorResponse(404, 'Route not found');
        } catch (HttpMethodNotAllowedException $e) {
            return $this->errorResponse(405, 'Method not allowed. Must be one of: ' . implode(', ', $e->getAllowedMethods()));
        } catch (HttpException $e) {
            return $this->errorResponse($e->getCode(), $e->getMessage());
        }
    }

    private function errorResponse(int $statusCode, string $message): ResponseInterface
    {
        $response = $this->responseFactory->createResponse($statusCode);
        $response->getBody()->write(json_encode(['error' => $message]));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
