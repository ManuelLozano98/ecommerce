<?php

namespace Tests\Middleware;

use App\Middleware\ServiceMiddleware;
use App\Exceptions\DeleteException;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DuplicateException;
use App\Exceptions\ForeignKeyException;
use App\Exceptions\NotFoundException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use Exception;

class ServiceMiddlewareTest extends TestCase
{
    private ServiceMiddleware $middleware;

    protected function setUp(): void
    {
        $factory = new class implements ResponseFactoryInterface {
            public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
            {
                return new Response($code);
            }
        };

        $this->middleware = new ServiceMiddleware($factory);
    }

    public function testPassThroughWhenNoException(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);

        $handler->method('handle')
            ->willReturn(new Response());

        $result = $this->middleware->process($request, $handler);

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
    }

    public function testDuplicateAndForeignKeyReturn409(): void
    {
        $this->assertExceptionMapsToStatus(
            new DuplicateException('duplicate'),
            409
        );

        $this->assertExceptionMapsToStatus(
            new ForeignKeyException('foreign key'),
            409
        );
    }

    public function testInsertUpdateDeleteReturn400(): void
    {
        $this->assertExceptionMapsToStatus(
            new InsertException('insert'),
            400
        );

        $this->assertExceptionMapsToStatus(
            new UpdateException('update'),
            400
        );

        $this->assertExceptionMapsToStatus(
            new DeleteException('delete'),
            400
        );
    }

    public function testNotFoundReturns404(): void
    {
        $this->assertExceptionMapsToStatus(
            new NotFoundException('not found'),
            404
        );
    }

    public function testGenericExceptionReturns500(): void
    {
        $this->assertExceptionMapsToStatus(
            new Exception('db failure'),
            500,
            'Database Error: db failure'
        );
    }

    private function assertExceptionMapsToStatus(
        Exception $exception,
        int $expectedStatus,
        ?string $expectedMessage = null
    ): void {
        $request = $this->createMock(ServerRequestInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);

        $handler->method('handle')
            ->willThrowException($exception);

        $result = $this->middleware->process($request, $handler);

        $this->assertEquals($expectedStatus, $result->getStatusCode());

        if ($expectedMessage !== null) {
            $body = (string) $result->getBody();
            $this->assertStringContainsString($expectedMessage, $body);
        }
    }
}
