<?php

namespace Tests\Middleware;

use App\Middleware\AdminMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AdminMiddlewareTest extends TestCase
{
    private AdminMiddleware $middleware;

    protected function setUp(): void
    {
        $_SESSION = [];

        $this->middleware = new AdminMiddleware();
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testReturns404WhenUserIsNotLoggedIn(): void
    {
        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $handler = $this->createMock(
            RequestHandlerInterface::class
        );

        $handler
            ->expects($this->never())
            ->method('handle');

        $response = ($this->middleware)(
            $request,
            $handler
        );

        $this->assertEquals(
            404,
            $response->getStatusCode()
        );
    }

    public function testReturns404WhenUserIsNotAdmin(): void
    {
        $_SESSION['user'] = [
            'roles' => ['user']
        ];

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $handler = $this->createMock(
            RequestHandlerInterface::class
        );

        $handler
            ->expects($this->never())
            ->method('handle');

        $response = ($this->middleware)(
            $request,
            $handler
        );

        $this->assertEquals(
            404,
            $response->getStatusCode()
        );
    }

    public function testCallsHandlerWhenUserIsAdmin(): void
    {
        $_SESSION['user'] = [
            'roles' => ['admin']
        ];

        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $expectedResponse = $this->createMock(
            ResponseInterface::class
        );

        $handler = $this->createMock(
            RequestHandlerInterface::class
        );

        $handler
            ->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturn($expectedResponse);

        $response = ($this->middleware)(
            $request,
            $handler
        );

        $this->assertSame(
            $expectedResponse,
            $response
        );
    }
}
