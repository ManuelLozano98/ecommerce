<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use App\Exceptions\DeleteException;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DuplicateException;
use App\Exceptions\ForeignKeyException;
use App\Exceptions\NotFoundException;
use Exception;

class ServiceMiddleware implements MiddlewareInterface
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
        } catch (DuplicateException | ForeignKeyException $e) {
            return $this->errorResponse(409, $e->getMessage());
        } catch (InsertException | UpdateException | DeleteException $e) {
            return $this->errorResponse(400, $e->getMessage());
        } catch (NotFoundException $e) {
            return $this->errorResponse(404, $e->getMessage());
        } catch (Exception $e) {
            return $this->errorResponse(500, 'Database Error: ' . $e->getMessage());
        }
    }

    private function errorResponse(int $statusCode, string $message): ResponseInterface
    {
        $response = $this->responseFactory->createResponse($statusCode);
        $response->getBody()->write(json_encode(['error' => $message]));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
