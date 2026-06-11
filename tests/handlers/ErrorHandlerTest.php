<?php

namespace Tests\Handlers;

use App\Handlers\ErrorHandler;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpMethodNotAllowedException;
use App\Exceptions\DuplicateException;
use Slim\Psr7\Uri;

class ErrorHandlerTest extends TestCase
{
    public function testApiNotFoundException(): void
    {
        $request = $this->createRequest('/api/test');

        $exception = new HttpNotFoundException($request);

        $response = ErrorHandler::handle($request, $exception, false);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertStringContainsString(
            'Route not found',
            (string) $response->getBody()
        );
    }

    public function testApiMethodNotAllowed(): void
    {
        $request = $this->createRequest('/api/test');

        $exception = new HttpMethodNotAllowedException(
            $request,
            'Not allowed'
        );

        $exception->setAllowedMethods(['GET', 'POST']);

        $response = ErrorHandler::handle(
            $request,
            $exception,
            false
        );

        $this->assertEquals(405, $response->getStatusCode());

        $this->assertStringContainsString(
            'GET, POST',
            (string) $response->getBody()
        );
    }

    public function testApiDuplicateException(): void
    {
        $request = $this->createRequest('/api/test');

        $exception = new DuplicateException(
            'Duplicate error'
        );

        $response = ErrorHandler::handle(
            $request,
            $exception,
            false
        );

        $this->assertEquals(409, $response->getStatusCode());

        $this->assertStringContainsString(
            'Duplicate error',
            (string) $response->getBody()
        );
    }

    public function testWebNotFoundRenders404View(): void
    {

        $request = $this->createRequest('/test');

        $exception = new HttpNotFoundException(
            $request
        );

        $response = ErrorHandler::handle(
            $request,
            $exception,
            false
        );

        $this->assertEquals(
            404,
            $response->getStatusCode()
        );

        $this->assertNotEmpty(
            (string) $response->getBody()
        );
    }

    public function testWebInternalErrorRenders500(): void
    {
        $request = $this->createRequest('/test');

        $exception = new \Exception(
            'fail'
        );

        $response = ErrorHandler::handle(
            $request,
            $exception,
            false
        );

        $this->assertEquals(
            500,
            $response->getStatusCode()
        );

        $this->assertNotEmpty(
            (string) $response->getBody()
        );
    }

    private function createRequest(
        string $path
    ): ServerRequestInterface {
        $request = $this->createMock(
            ServerRequestInterface::class
        );

        $uri = new Uri(
            '',
            '',
            80,
            $path
        );

        $request
            ->method('getUri')
            ->willReturn($uri);

        return $request;
    }
}
