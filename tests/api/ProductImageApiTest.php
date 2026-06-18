<?php

namespace Tests\Unit\Api;

use PHPUnit\Framework\TestCase;
use App\Api\ProductImageApi;
use App\Services\ProductImageService;
use Rakit\Validation\Validator;
use Rakit\Validation\ErrorBag;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

class ProductImageApiTest extends TestCase
{
    private ProductImageService $service;
    private Validator $validator;
    private ProductImageApi $api;

    protected function setUp(): void
    {
        $this->service = $this->createMock(ProductImageService::class);
        $this->validator = $this->createMock(Validator::class);

        $this->api = new ProductImageApi($this->service, $this->validator);
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
        array $body = [],
        array $files = [],
        string $method = 'GET'
    ): ServerRequestInterface {
        $request = $this->createMock(ServerRequestInterface::class);

        $request->method('getQueryParams')
            ->willReturn($query);

        $request->method('getParsedBody')
            ->willReturn($body);

        $request->method('getUploadedFiles')
            ->willReturn($files);

        $request->method('getMethod')
            ->willReturn($method);

        return $request;
    }


    public function testGetAllWithoutPagination()
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

    public function testGetGalleryDetailed()
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([]);

        $result = $this->api->getGalleryDetailed($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testGetByProduct()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('getByProductId')
            ->with(1)
            ->willReturn([]);

        $result = $this->api->getByProduct(
            $request,
            $response,
            ['product_id' => 1]
        );

        $this->assertSame($response, $result);
    }

    public function testGetOne()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('getImage')
            ->with(5)
            ->willReturn([]);

        $result = $this->api->getOne(
            $request,
            $response,
            ['id' => 5]
        );

        $this->assertSame($response, $result);
    }


    public function testSaveImageSuccess()
    {
        $file = $this->createMock(UploadedFileInterface::class);
        $file->method('getClientFilename')->willReturn('image.jpg');

        $request = $this->createRequest(
            [],
            ['product_id' => 1, 'type' => 'main'],
            ['image' => $file],
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

    public function testSaveWithoutImage()
    {
        $request = $this->createRequest(
            [],
            ['product_id' => 1, 'type' => 'main'],
            [],
            'POST'
        );

        $response = $this->createResponse();

        $result = $this->api->save($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testSaveInvalidImage()
    {
        $file = $this->createMock(UploadedFileInterface::class);
        $file->method('getClientFilename')->willReturn('virus.exe');

        $request = $this->createRequest(
            [],
            ['product_id' => 1, 'type' => 'main'],
            ['image' => $file],
            'POST'
        );

        $response = $this->createResponse();

        $this->service
            ->expects($this->never())
            ->method('save');

        $result = $this->api->save($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testSaveValidationFails()
    {
        $file = $this->createMock(UploadedFileInterface::class);
        $file->method('getClientFilename')->willReturn('image.jpg');

        $errors = $this->createMock(ErrorBag::class);
        $errors->method('toArray')->willReturn(['product_id' => ['required']]);

        $validation = $this->createMock(\Rakit\Validation\Validation::class);
        $validation->method('validate');
        $validation->method('fails')->willReturn(true);
        $validation->method('errors')->willReturn($errors);

        $this->validator
            ->method('make')
            ->willReturn($validation);

        $request = $this->createRequest(
            [],
            ['product_id' => 1, 'type' => 'main'],
            ['image' => $file],
            'POST'
        );

        $response = $this->createResponse();

        $this->service
            ->expects($this->never())
            ->method('save');

        $result = $this->api->save($request, $response, []);

        $this->assertSame($response, $result);
    }


    public function testUpdateImage()
    {
        $request = $this->createRequest(
            [],
            ['product_id' => 1, 'type' => 'main'],
            [],
            'POST'
        );

        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('update')
            ->with($this->arrayHasKey('id'))
            ->willReturn([]);

        $result = $this->api->update(
            $request,
            $response,
            ['id' => 1]
        );

        $this->assertSame($response, $result);
    }

    public function testUpdateWithImage()
    {
        $file = $this->createMock(UploadedFileInterface::class);
        $file->method('getClientFilename')->willReturn('image.jpg');

        $request = $this->createRequest(
            [],
            ['product_id' => 1, 'type' => 'main'],
            ['image' => $file],
            'POST'
        );

        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('update')
            ->willReturn([]);

        $result = $this->api->update(
            $request,
            $response,
            ['id' => 1]
        );

        $this->assertSame($response, $result);
    }

    public function testUpdateInvalidImage()
    {
        $file = $this->createMock(UploadedFileInterface::class);
        $file->method('getClientFilename')->willReturn('virus.exe');

        $request = $this->createRequest(
            [],
            ['product_id' => 1, 'type' => 'main'],
            ['image' => $file],
            'POST'
        );

        $response = $this->createResponse();

        $this->service
            ->expects($this->never())
            ->method('update');

        $result = $this->api->update(
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
