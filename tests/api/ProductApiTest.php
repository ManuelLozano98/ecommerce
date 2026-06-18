<?php

namespace Tests\Unit\Api;

use PHPUnit\Framework\TestCase;
use App\Api\ProductApi;
use App\Services\ProductService;
use App\Utils\ApiHelper;
use Rakit\Validation\Validator;
use App\Dtos\ProductNameDTO;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ProductApiTest extends TestCase
{
    private ProductService $service;
    private Validator $validator;
    private ProductApi $api;

    protected function setUp(): void
    {
        $this->service = $this->createMock(ProductService::class);
        $this->validator = $this->createMock(Validator::class);
        $this->api = new ProductApi($this->service, $this->validator);
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

    private function createRequest(array $query = [], string $body = '', string $method = 'GET'): ServerRequestInterface
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn($body);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn($query);
        $request->method('getBody')->willReturn($stream);
        $request->method('getMethod')->willReturn($method);

        return $request;
    }

    public function testGetAllReturnsProducts(): void
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $this->service->method('getAll')->willReturn([['id' => 1]]);

        $result = $this->api->getAll($request, $response, []);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testGetAllWithPagination(): void
    {
        $request = $this->createRequest(['start' => 0, 'length' => 10, 'draw' => 1]);
        $response = $this->createResponse();

        $this->service->method('paginate')->willReturn([
            'recordsTotal' => 1,
            'recordsFiltered' => 1,
            'data' => [['id' => 1]]
        ]);

        $result = $this->api->getAll($request, $response, []);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testGetProductById(): void
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $this->service->method('getProduct')->willReturn(['id' => 1]);

        $result = $this->api->getProductById($request, $response, ['id' => 1]);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testSaveProduct(): void
    {
        $request = $this->createRequest([], json_encode([
            'name' => 'Product',
            'code' => 'P1',
            'category_id' => 1
        ]), 'POST');

        $response = $this->createResponse();

        $validation = $this->createMock(\Rakit\Validation\Validation::class);

        $validation->method('validate')->willReturn(true);
        $validation->method('fails')->willReturn(false);

        $this->validator
            ->method('make')
            ->willReturn($validation);

        $this->service
            ->method('save')
            ->willReturn(['id' => 1]);

        $result = $this->api->save($request, $response, []);

        $this->assertInstanceOf(\Psr\Http\Message\ResponseInterface::class, $result);
    }

    public function testSaveInvalidJson(): void
    {
        $request = $this->createRequest([], 'INVALID', 'POST');
        $response = $this->createResponse();

        $result = $this->api->save($request, $response, []);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testDeleteProduct(): void
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $this->service->method('delete')->willReturn(null);

        $result = $this->api->delete($request, $response, ['id' => 1]);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testGetProductByCode(): void
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('getProductByCode')
            ->with('ABC123')
            ->willReturn(['id' => 1]);

        $result = $this->api->getProductByCode(
            $request,
            $response,
            ['code' => 'ABC123']
        );

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testGetProductsDetailedWithPagination(): void
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

        $result = $this->api->getProductsDetailed(
            $request,
            $response,
            []
        );

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testGetProductsDetailedWithoutPagination(): void
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([]);

        $result = $this->api->getProductsDetailed(
            $request,
            $response,
            []
        );

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testGetAllWithFilters(): void
    {
        $params = [
            'search' => 'mouse',
            'categories' => [1, 2],
            'scores' => [4, 5],
            'range_price' => [0, 100],
            'sort' => 'price_asc',
            'limit' => 10,
            'offset' => 0
        ];

        $request = $this->createRequest($params);
        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('getProductsFiltered')
            ->with(
                [
                    'search' => 'mouse',
                    'categories' => [1, 2],
                    'scores' => [4, 5],
                    'prices' => [0, 100],
                    'sort' => 'price_asc'
                ],
                10,
                0
            )
            ->willReturn([]);

        $result = $this->api->getAll($request, $response, []);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testGetProductsName(): void
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $product = $this->createMock(\App\Models\Product::class);

        $product->method('getId')->willReturn(1);
        $product->method('getName')->willReturn('Mouse');

        $this->service
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([$product]);

        $result = $this->api->getProductsName(
            $request,
            $response,
            []
        );

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testUpdateProduct(): void
    {
        $request = $this->createRequest(
            [],
            json_encode([
                'name' => 'Product',
                'code' => 'P1',
                'category_id' => 1
            ]),
            'PUT'
        );

        $response = $this->createResponse();

        $validation = $this->createMock(\Rakit\Validation\Validation::class);

        $validation->method('validate')->willReturn(true);
        $validation->method('fails')->willReturn(false);

        $this->validator
            ->method('make')
            ->willReturn($validation);

        $this->service
            ->expects($this->once())
            ->method('update')
            ->with([
                'id' => 5,
                'name' => 'Product',
                'code' => 'P1',
                'category_id' => 1
            ]);

        $this->api->save(
            $request,
            $response,
            ['id' => 5]
        );
    }

    public function testSaveInvalidData(): void
    {
        $request = $this->createRequest(
            [],
            json_encode([
                'name' => ''
            ]),
            'POST'
        );

        $response = $this->createResponse();

        $errors = $this->createMock(\Rakit\Validation\ErrorBag::class);

        $validation = $this->createMock(\Rakit\Validation\Validation::class);

        $validation->method('fails')->willReturn(true);
        $validation->method('errors')->willReturn($errors);

        $this->validator
            ->method('make')
            ->willReturn($validation);

        $this->service
            ->expects($this->never())
            ->method('save');

        $this->api->save($request, $response, []);
    }

    public function testSaveImage(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createResponse();

        $file = $this->createMock(\Psr\Http\Message\UploadedFileInterface::class);

        $file->method('getClientFilename')
            ->willReturn('image.jpg');

        $request->method('getUploadedFiles')
            ->willReturn([
                'image' => $file
            ]);

        $this->service
            ->expects($this->once())
            ->method('saveImage')
            ->with($file, 1)
            ->willReturn('test.jpg');

        $result = $this->api->saveImage(
            $request,
            $response,
            ['id' => 1]
        );

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testSaveImageWithoutFile(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getUploadedFiles')
            ->willReturn([]);

        $response = $this->createResponse();

        $result = $this->api->saveImage(
            $request,
            $response,
            ['id' => 1]
        );

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testSaveImageInvalidExtension(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);

        $file = $this->createMock(\Psr\Http\Message\UploadedFileInterface::class);

        $file->method('getClientFilename')
            ->willReturn('virus.exe');

        $request->method('getUploadedFiles')
            ->willReturn([
                'image' => $file
            ]);

        $response = $this->createResponse();

        $result = $this->api->saveImage(
            $request,
            $response,
            ['id' => 1]
        );

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
}
