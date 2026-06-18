<?php

namespace Tests\Unit\Api;

use PHPUnit\Framework\TestCase;
use App\Models\Role;
use App\Api\RoleApi;
use App\Services\RoleService;
use App\Utils\ApiHelper;
use Rakit\Validation\Validator;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class RoleApiTest extends TestCase
{
    private RoleService $service;
    private Validator $validator;
    private RoleApi $api;

    protected function setUp(): void
    {
        $this->service = $this->createMock(RoleService::class);
        $this->validator = $this->createMock(Validator::class);

        $this->api = new RoleApi($this->service, $this->validator);
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

    public function testGetAllReturnsRoles(): void
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $this->service
            ->method('getAll')
            ->willReturn([['id' => 1, 'name' => 'Admin']]);

        $result = $this->api->getAll($request, $response, []);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testGetAllWithPagination(): void
    {
        $request = $this->createRequest([
            'start' => 0,
            'length' => 10,
            'draw' => 1
        ]);

        $response = $this->createResponse();

        $this->service
            ->method('paginate')
            ->willReturn([
                'recordsTotal' => 1,
                'recordsFiltered' => 1,
                'data' => [['id' => 1]]
            ]);

        $result = $this->api->getAll($request, $response, []);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testGetRoleById(): void
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $this->service
            ->method('getRole')
            ->with(1)
            ->willReturn(['id' => 1, 'name' => 'Admin']);

        $result = $this->api->getRoleById(
            $request,
            $response,
            ['id' => 1]
        );

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testGetRolesName(): void
    {

        $request = $this->createRequest();
        $response = $this->createResponse();

        $role = $this->createMock(Role::class);
        $role->method('getId')->willReturn(1);
        $role->method('getName')->willReturn('Admin');

        $this->service
            ->method('getAll')
            ->willReturn([$role]);

        $result = $this->api->getRolesName($request, $response, []);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }

    public function testSaveRole(): void
    {
        $request = $this->createRequest(
            [],
            json_encode([
                'name' => 'Admin',
                'active' => true,
                'description' => 'Role admin'
            ]),
            'POST'
        );

        $response = $this->createResponse();

        $validation = $this->createMock(\Rakit\Validation\Validation::class);
        $validation->method('fails')->willReturn(false);

        $this->validator
            ->method('make')
            ->willReturn($validation);

        $this->service
            ->expects($this->once())
            ->method('save');

        $this->api->save($request, $response, []);

        $this->assertTrue(true);
    }

    public function testUpdateRole(): void
    {
        $request = $this->createRequest(
            [],
            json_encode([
                'name' => 'Updated Role',
                'active' => true
            ]),
            'PUT'
        );

        $response = $this->createResponse();

        $validation = $this->createMock(\Rakit\Validation\Validation::class);
        $validation->method('fails')->willReturn(false);

        $this->validator
            ->method('make')
            ->willReturn($validation);

        $this->service
            ->expects($this->once())
            ->method('update')
            ->with([
                'id' => 1,
                'name' => 'Updated Role',
                'active' => true
            ]);

        $this->api->save($request, $response, ['id' => 1]);

        $this->assertTrue(true);
    }

    public function testDeleteRole(): void
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

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
}
