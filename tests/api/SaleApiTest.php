<?php

namespace Tests\Unit\Api;

use PHPUnit\Framework\TestCase;
use App\Api\SaleApi;
use App\Models\Sale;
use App\Services\SaleService;
use Rakit\Validation\Validator;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Rakit\Validation\ErrorBag;

class SaleApiTest extends TestCase
{

    private SaleService $service;
    private Validator $validator;
    private SaleApi $api;


    protected function setUp(): void
    {
        $this->service = $this->createMock(SaleService::class);
        $this->validator = $this->createMock(Validator::class);
        $this->api = new SaleApi($this->service, $this->validator);
    }

    private function createRequest(
        array $query = [],
        string $body = '',
        string $method = 'GET'
    ): ServerRequestInterface {
        $stream = $this->createMock(StreamInterface::class);

        $stream->method('getContents')
            ->willReturn($body);

        $request = $this->createMock(ServerRequestInterface::class);

        $request->method('getQueryParams')
            ->willReturn($query);

        $request->method('getBody')
            ->willReturn($stream);

        $request->method('getMethod')
            ->willReturn($method);

        return $request;
    }

    public function testGetAll()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('getSales')
            ->willReturn([]);

        $result = $this->api->getAll($request, $response, []);

        $this->assertSame($response, $result);
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

    public function testGetAllDetailed()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('getSalesDetailed')
            ->willReturn([]);

        $result = $this->api->getAllDetailed($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testGetSale()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('getSale')
            ->with(1)
            ->willReturn([]);

        $result = $this->api->getSale(
            $request,
            $response,
            ['id' => 1]
        );

        $this->assertSame($response, $result);
    }

    public function testGetDetailedSale()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('getDetailedSale')
            ->with(1)
            ->willReturn([]);

        $result = $this->api->getDetailedSale(
            $request,
            $response,
            ['id' => 1]
        );

        $this->assertSame($response, $result);
    }

    public function testGetPurchasesByUser()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createResponse();

        $sale = $this->createMock(Sale::class);

        $sale->method('toArray')
            ->willReturn(['id' => 1]);

        $this->service
            ->expects($this->once())
            ->method('getPurchasesByUser')
            ->with(5)
            ->willReturn([$sale]);

        $result = $this->api->getPurchasesbyUser(
            $request,
            $response,
            ['user_id' => 5]
        );

        $this->assertSame($response, $result);
    }

    public function testGetSalesWithUserAndItems()
    {
        $request = $this->createMock(ServerRequestInterface::class);

        $request->method('getQueryParams')
            ->willReturn([]);

        $response = $this->createResponse();

        $sale = $this->createMock(Sale::class);

        $sale->method('toArray')
            ->willReturn(['id' => 1]);

        $this->service
            ->expects($this->once())
            ->method('getSalesWithUserAndItems')
            ->willReturn([$sale]);

        $result = $this->api->getSalesWithUserAndItems(
            $request,
            $response,
            []
        );

        $this->assertSame($response, $result);
    }

    public function testGetSalesWithUserAndItemsPagination()
    {
        $request = $this->createMock(ServerRequestInterface::class);

        $request->method('getQueryParams')
            ->willReturn([
                'start' => 0,
                'length' => 10,
                'draw' => 1
            ]);

        $response = $this->createResponse();

        $sale = $this->createMock(Sale::class);

        $sale->method('toArray')
            ->willReturn(['id' => 1]);

        $this->service
            ->expects($this->once())
            ->method('getSalesWithUserAndItems')
            ->willReturn([$sale]);

        $result = $this->api->getSalesWithUserAndItems(
            $request,
            $response,
            []
        );

        $this->assertSame($response, $result);
    }

    public function testSaveSale()
    {
        $request = $this->createRequest(
            [],
            json_encode([
                'user_id' => 1,
                'total_amount' => '100.50',
                'payment_method' => 'cash',
                'status' => 'pending'
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

        $this->api->save($request, $response, []);
    }

    public function testUpdateSale()
    {
        $request = $this->createRequest(
            [],
            json_encode([
                'user_id' => 1,
                'total_amount' => '100.50',
                'payment_method' => 'cash',
                'status' => 'pending'
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
            ->with([
                'id' => 10,
                'user_id' => 1,
                'total_amount' => '100.50',
                'payment_method' => 'cash',
                'status' => 'pending'
            ]);

        $this->api->save(
            $request,
            $response,
            ['id' => 10]
        );
    }

    public function testSaveInvalidData()
    {
        $request = $this->createRequest(
            [],
            json_encode([]),
            'POST'
        );

        $response = $this->createResponse();

        $errors = $this->createMock(ErrorBag::class);

        $errors->method('toArray')
            ->willReturn([
                'user_id' => ['required']
            ]);

        $validation = $this->createMock(\Rakit\Validation\Validation::class);

        $validation->method('fails')
            ->willReturn(true);

        $validation->method('errors')
            ->willReturn($errors);

        $this->validator
            ->method('make')
            ->willReturn($validation);

        $this->service
            ->expects($this->never())
            ->method('save');

        $result = $this->api->save($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testDeleteSale()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('deleteSale')
            ->with(1);

        $result = $this->api->delete(
            $request,
            $response,
            ['id' => 1]
        );

        $this->assertSame($response, $result);
    }
}
