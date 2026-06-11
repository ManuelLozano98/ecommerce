<?php

namespace Tests\Unit\Api;

use PHPUnit\Framework\TestCase;
use App\Api\ProductInformationApi;
use App\Services\ProductInformationService;
use Rakit\Validation\Validator;
use Rakit\Validation\ErrorBag;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ProductInformationApiTest extends TestCase
{
    private ProductInformationService $service;
    private Validator $validator;
    private ProductInformationApi $api;

    protected function setUp(): void
    {
        $this->service = $this->createMock(ProductInformationService::class);
        $this->validator = $this->createMock(Validator::class);

        $this->api = new ProductInformationApi($this->service, $this->validator);
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


    public function testGetAll()
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

    public function testGetAllWithPagination()
    {
        $request = $this->createRequest([
            'start' => 0,
            'length' => 10,
            'draw' => 1
        ]);

        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('paginate')
            ->willReturn([
                'recordsTotal' => 1,
                'recordsFiltered' => 1,
                'data' => []
            ]);

        $result = $this->api->getAll($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testGetDetailedWithPagination()
    {
        $request = $this->createRequest([
            'start' => 0,
            'length' => 10,
            'draw' => 1
        ]);

        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('paginateDetailed')
            ->willReturn([
                'recordsTotal' => 1,
                'recordsFiltered' => 1,
                'data' => []
            ]);

        $result = $this->api->getProductInformationDetailed($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testGetOne()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('getById')
            ->with(5)
            ->willReturn([]);

        $result = $this->api->getOne(
            $request,
            $response,
            ['id' => 5]
        );

        $this->assertSame($response, $result);
    }

    public function testGetByProduct()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('getByProductId')
            ->with(10)
            ->willReturn([]);

        $result = $this->api->getByProduct(
            $request,
            $response,
            ['product_id' => 10]
        );

        $this->assertSame($response, $result);
    }

    public function testGetFeatured()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('getFeatured')
            ->willReturn([]);

        $result = $this->api->getFeatured($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testGetWithDiscount()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('getWithDiscount')
            ->willReturn([]);

        $result = $this->api->getWithDiscount($request, $response, []);

        $this->assertSame($response, $result);
    }


    public function testSaveSuccess()
    {
        $request = $this->createRequest(
            [],
            json_encode([
                'product_id' => 1,
                'brand' => 'Test',
                'rating_average' => 4.5
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
            ->method('save')
            ->willReturn([]);

        $result = $this->api->save($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testSaveValidationFails()
    {
        $request = $this->createRequest(
            [],
            json_encode(['product_id' => 0]),
            'POST'
        );

        $response = $this->createResponse();

        $errors = $this->createMock(ErrorBag::class);
        $errors->method('toArray')->willReturn(['product_id' => ['invalid']]);

        $validation = $this->createMock(\Rakit\Validation\Validation::class);
        $validation->method('validate');
        $validation->method('fails')->willReturn(true);
        $validation->method('errors')->willReturn($errors);

        $this->validator
            ->method('make')
            ->willReturn($validation);

        $this->service
            ->expects($this->never())
            ->method('save');

        $result = $this->api->save($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testUpdateSuccess()
    {
        $request = $this->createRequest(
            [],
            json_encode([
                'product_id' => 1,
                'brand' => 'Updated'
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
            ->method('update')
            ->willReturn([]);

        $result = $this->api->save(
            $request,
            $response,
            ['id' => 1]
        );

        $this->assertSame($response, $result);
    }


    public function testDelete()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('delete')
            ->with(1);

        $result = $this->api->delete(
            $request,
            $response,
            ['id' => 1]
        );

        $this->assertSame($response, $result);
    }
}