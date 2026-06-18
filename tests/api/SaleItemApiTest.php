<?php

namespace Tests\Unit\Api;

use PHPUnit\Framework\TestCase;
use App\Api\SaleItemApi;
use App\Services\SaleItemService;
use Rakit\Validation\Validator;
use Rakit\Validation\ErrorBag;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class SaleItemApiTest extends TestCase
{
    private SaleItemService $service;
    private Validator $validator;
    private SaleItemApi $api;

    protected function setUp(): void
    {
        $this->service = $this->createMock(SaleItemService::class);
        $this->validator = $this->createMock(Validator::class);

        $this->api = new SaleItemApi($this->service, $this->validator);
    }

    private function createRequest(
        array $query = [],
        string $body = '',
        string $method = 'GET'
    ): ServerRequestInterface {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn($body);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn($query);
        $request->method('getBody')->willReturn($stream);
        $request->method('getMethod')->willReturn($method);

        return $request;
    }

    private function createResponse(): ResponseInterface
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('write')->willReturn(0);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($stream);
        $response->method('withStatus')->willReturnSelf();
        $response->method('withHeader')->willReturnSelf();

        return $response;
    }

    public function testGetAll(): void
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([]);

        $result = $this->api->getAll($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testSavePostSuccess(): void
    {
        $request = $this->createRequest(
            [],
            json_encode([
                'product_id' => 1,
                'quantity' => 2,
                'price' => '10.00',
                'subtotal' => '20.00'
            ]),
            'POST'
        );

        $response = $this->createResponse();

        $validation = $this->createMock(\Rakit\Validation\Validation::class);
        $validation->method('validate');
        $validation->method('fails')->willReturn(false);

        $this->validator
            ->method('make')
            ->willReturn($validation);

        $this->service
            ->expects($this->once())
            ->method('save');

        $result = $this->api->save($request, $response, ['id' => 1]);

        $this->assertSame($response, $result);
    }

    public function testSavePutSuccess(): void
    {
        $request = $this->createRequest(
            [],
            json_encode([
                'product_id' => 1,
                'quantity' => 2,
                'price' => '10.00',
                'subtotal' => '20.00'
            ]),
            'PUT'
        );

        $response = $this->createResponse();

        $validation = $this->createMock(\Rakit\Validation\Validation::class);
        $validation->method('validate');
        $validation->method('fails')->willReturn(false);

        $this->validator
            ->method('make')
            ->willReturn($validation);

        $this->service
            ->expects($this->once())
            ->method('update');

        $result = $this->api->save($request, $response, [
            'id' => 1,
            'item_id' => 10
        ]);

        $this->assertSame($response, $result);
    }

    public function testSaveInvalidData(): void
    {
        $request = $this->createRequest(
            [],
            json_encode([]),
            'POST'
        );

        $response = $this->createResponse();

        $errors = $this->createMock(ErrorBag::class);
        $errors->method('toArray')->willReturn([
            'product_id' => ['required']
        ]);

        $validation = $this->createMock(\Rakit\Validation\Validation::class);
        $validation->method('fails')->willReturn(true);
        $validation->method('errors')->willReturn($errors);

        $this->validator
            ->method('make')
            ->willReturn($validation);

        $this->service
            ->expects($this->never())
            ->method('save');

        $result = $this->api->save($request, $response, ['id' => 1]);

        $this->assertSame($response, $result);
    }

    public function testDelete(): void
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('deleteItemById')
            ->with(10, 1);

        $result = $this->api->delete(
            $request,
            $response,
            ['item_id' => 10, 'id' => 1]
        );

        $this->assertSame($response, $result);
    }
}
