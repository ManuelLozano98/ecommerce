<?php

namespace Tests\Middleware;

use App\Middleware\TokenMiddleware;
use App\Services\PasswordResetService;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\Route;
use Slim\Routing\RouteContext;

class TokenMiddlewareTest extends TestCase
{
    private PasswordResetService $passwordService;
    private TokenMiddleware $middleware;

    protected function setUp(): void
    {

        // if (!defined('ROOT')) {
        //     define('ROOT', '/test');
        // }

        $this->passwordService = $this->createMock(
            PasswordResetService::class
        );

        $this->middleware = new TokenMiddleware(
            $this->passwordService
        );
    }

    public function testRedirectsWhenTokenDoesNotExist(): void
    {
        $request = $this->createRequestWithToken(
            'invalid-token'
        );

        $handler = $this->createMock(
            RequestHandlerInterface::class
        );

        $handler
            ->expects($this->never())
            ->method('handle');

        $this->passwordService
            ->expects($this->once())
            ->method('getByToken')
            ->with('invalid-token')
            ->willReturn(null);

        $response = ($this->middleware)(
            $request,
            $handler
        );

        $this->assertEquals(
            302,
            $response->getStatusCode()
        );

        $this->assertStringContainsString(
            'forgot-password?error=invalid-link',
            $response->getHeaderLine('Location')
        );
    }

    public function testRedirectsWhenTokenHasAlreadyBeenUsed(): void
    {
        $request = $this->createRequestWithToken(
            'used-token'
        );

        $passwordReset = $this->createMock(\App\Models\PasswordReset::class);

        $passwordReset
            ->method('isUsed')
            ->willReturn(true);

        $passwordReset
            ->method('isUsed')
            ->willReturn(true);

        $handler = $this->createMock(
            RequestHandlerInterface::class
        );

        $handler
            ->expects($this->never())
            ->method('handle');

        $this->passwordService
            ->expects($this->once())
            ->method('getByToken')
            ->with('used-token')
            ->willReturn($passwordReset);

        $response = ($this->middleware)(
            $request,
            $handler
        );

        $this->assertEquals(
            302,
            $response->getStatusCode()
        );

        $this->assertStringContainsString(
            'forgot-password?error=invalid-link',
            $response->getHeaderLine('Location')
        );
    }

    public function testCallsHandlerWhenTokenIsValid(): void
    {
        $request = $this->createRequestWithToken(
            'valid-token'
        );

        $passwordReset = $this->createMock(\App\Models\PasswordReset::class);

        $passwordReset
            ->method('isUsed')
            ->willReturn(false);

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

        $this->passwordService
            ->expects($this->once())
            ->method('getByToken')
            ->with('valid-token')
            ->willReturn($passwordReset);

        $response = ($this->middleware)(
            $request,
            $handler
        );

        $this->assertSame(
            $expectedResponse,
            $response
        );
    }

    private function createRequestWithToken(
        string $token
    ): ServerRequestInterface {
        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $route = $this->createMock(
            Route::class
        );

        $route
            ->method('getArguments')
            ->willReturn([
                'token' => $token
            ]);

        $request
            ->method('getAttribute')
            ->willReturnMap([
                [RouteContext::ROUTE, $route]
            ]);

        return $request;
    }
}
