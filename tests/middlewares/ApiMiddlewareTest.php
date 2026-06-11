<?php

namespace Tests\Middleware;

use App\Middleware\ApiMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpException;
use Slim\Psr7\Response;

class ApiMiddlewareTest extends TestCase
{
    private ApiMiddleware $middleware;

    protected function setUp(): void
    {
        $responseFactory = new class implements ResponseFactoryInterface {
            public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
            {
                return new Response($code);
            }
        };

        $this->middleware = new ApiMiddleware($responseFactory);
    }

    public function testPassesThroughWhenNoException(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')
            ->willReturn(new Response());

        $result = $this->middleware->process($request, $handler);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testHandlesNotFoundException(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')
            ->willThrowException(new HttpNotFoundException($request));

        $result = $this->middleware->process($request, $handler);

        $this->assertEquals(404, $result->getStatusCode());
    }

    public function testHandlesMethodNotAllowedException(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')
            ->willThrowException(
                new HttpMethodNotAllowedException($request)
            );

        $result = $this->middleware->process($request, $handler);

        $this->assertEquals(405, $result->getStatusCode());
    }

    public function testHandlesGenericHttpException(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')
            ->willThrowException(
                new HttpException($request, 'Custom error', 400)
            );

        $result = $this->middleware->process($request, $handler);

        $this->assertEquals(400, $result->getStatusCode());
    }
}
