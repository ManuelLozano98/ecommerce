<?php

namespace Tests\Unit\Api;

use PHPUnit\Framework\TestCase;
use App\Api\UserRoleApi;
use App\Services\UserRoleService;
use App\Services\RoleService;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class UserRoleApiTest extends TestCase
{
    private UserRoleService $userRoleService;
    private RoleService $roleService;
    private UserRoleApi $api;

    protected function setUp(): void
    {
        $this->userRoleService = $this->createMock(UserRoleService::class);
        $this->roleService = $this->createMock(RoleService::class);

        $this->api = new UserRoleApi(
            $this->userRoleService,
            $this->roleService
        );
    }

    private function createRequest(array $query = [], string $body = '', string $method = 'GET')
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn($body);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn($query);
        $request->method('getBody')->willReturn($stream);
        $request->method('getMethod')->willReturn($method);

        return $request;
    }

    private function createResponse()
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('write')->willReturn(0);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($stream);
        $response->method('withStatus')->willReturnSelf();
        $response->method('withHeader')->willReturnSelf();

        return $response;
    }

    public function testGetAll()
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $this->userRoleService
            ->expects($this->once())
            ->method('getUserRoles')
            ->willReturn([]);

        $result = $this->api->getAll($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testGetUserRolesDetailedWithoutPagination()
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $this->userRoleService
            ->expects($this->once())
            ->method('getUserRolesDetailed')
            ->willReturn([]);

        $result = $this->api->getUserRolesDetailed($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testGetUserRolesDetailedWithPagination()
    {
        $request = $this->createRequest([
            'start' => 0,
            'length' => 10
        ]);

        $response = $this->createResponse();

        $this->userRoleService
            ->method('getUserRolesDetailed')
            ->willReturn([]);

        $result = $this->api->getUserRolesDetailed($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testSave()
    {
        $request = $this->createRequest(
            [],
            json_encode(['role_id' => 1]),
            'POST'
        );

        $response = $this->createResponse();

        $this->userRoleService
            ->expects($this->once())
            ->method('save')
            ->with([
                'role_id' => 1,
                'user_id' => 5
            ])
            ->willReturn(['id' => 10]);

        $result = $this->api->save(
            $request,
            $response,
            ['id' => 5]
        );

        $this->assertSame($response, $result);
    }

    public function testDeleteByRole()
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $userRoleMock = new class {
            public function getId()
            {
                return 10;
            }
        };

        $this->userRoleService
            ->method('getUserRoleByRole')
            ->willReturn([$userRoleMock]);

        $this->userRoleService
            ->expects($this->once())
            ->method('delete')
            ->with(10);

        $this->roleService
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

    public function testDeleteRolesByUserId()
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $mock = new class {
            public function getId()
            {
                return 7;
            }
        };

        $this->userRoleService
            ->method('getUserRolesbyUserId')
            ->willReturn([$mock]);

        $this->userRoleService
            ->expects($this->once())
            ->method('delete')
            ->with(7);

        $result = $this->api->deleteRolesByUserId(
            $request,
            $response,
            ['user_id' => 3]
        );

        $this->assertSame($response, $result);
    }

    public function testDeleteByUserIdAndRoleId()
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $mock = new class {
            public function getId()
            {
                return 99;
            }
        };

        $this->userRoleService
            ->method('getUserRolebyUserIdAndRoleId')
            ->willReturn($mock);

        $this->userRoleService
            ->expects($this->once())
            ->method('delete')
            ->with(99);

        $result = $this->api->deletebyUserIdAndRoleId(
            $request,
            $response,
            ['id' => 1, 'role_id' => 2]
        );

        $this->assertSame($response, $result);
    }
}
