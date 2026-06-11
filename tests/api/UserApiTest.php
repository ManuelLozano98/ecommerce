<?php

namespace Tests\Unit\Api;

use PHPUnit\Framework\TestCase;
use App\Api\UserApi;
use App\Services\UserService;
use App\Services\DocumentTypeService;
use Rakit\Validation\Validator;
use Rakit\Validation\ErrorBag;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class UserApiTest extends TestCase
{
    private UserService $userService;
    private DocumentTypeService $documentService;
    private Validator $validator;
    private UserApi $api;

    protected function setUp(): void
    {
        $this->userService = $this->createMock(UserService::class);
        $this->documentService = $this->createMock(DocumentTypeService::class);
        $this->validator = $this->createMock(Validator::class);

        $this->api = new UserApi(
            $this->userService,
            $this->documentService,
            $this->validator
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

        $this->userService
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([]);

        $result = $this->api->getAll($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testGetUserById()
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $this->userService
            ->expects($this->once())
            ->method('getUser')
            ->with(1)
            ->willReturn(['id' => 1]);

        $result = $this->api->getUserById($request, $response, ['id' => 1]);

        $this->assertSame($response, $result);
    }

    public function testGetUsernames()
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $this->userService->method('getAll')->willReturn([]);

        $result = $this->api->getUsernames($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testSavePostSuccess()
    {
        $request = $this->createRequest(
            [],
            json_encode([
                'name' => 'John',
                'email' => 'john@test.com',
                'password' => 'Aa123456!',
                'username' => 'john123'
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

        $this->userService
            ->expects($this->once())
            ->method('save');

        $result = $this->api->save($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testSavePutSuccess()
    {
        $request = $this->createRequest(
            [],
            json_encode([
                'name' => 'John',
                'email' => 'john@test.com',
                'username' => 'john123'
            ]),
            'PUT'
        );

        $response = $this->createResponse();

        $validation = $this->createMock(\Rakit\Validation\Validation::class);
        $validation->method('fails')->willReturn(false);

        $this->validator->method('make')->willReturn($validation);

        $this->userService
            ->expects($this->once())
            ->method('update');

        $result = $this->api->save($request, $response, ['id' => 1]);

        $this->assertSame($response, $result);
    }

    public function testSaveInvalidJson()
    {
        $request = $this->createRequest([], '', 'POST');
        $response = $this->createResponse();

        $result = $this->api->save($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testSaveInvalidData()
    {
        $request = $this->createRequest([], json_encode([]), 'POST');
        $response = $this->createResponse();

        $errors = $this->createMock(ErrorBag::class);
        $errors->method('toArray')->willReturn(['email' => ['required']]);

        $validation = $this->createMock(\Rakit\Validation\Validation::class);
        $validation->method('fails')->willReturn(true);
        $validation->method('errors')->willReturn($errors);

        $this->validator->method('make')->willReturn($validation);

        $this->userService->expects($this->never())->method('save');

        $result = $this->api->save($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testDelete()
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $this->userService
            ->expects($this->once())
            ->method('delete');

        $result = $this->api->delete($request, $response, ['id' => 1]);

        $this->assertSame($response, $result);
    }

    public function testGetUsersDetailed()
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $this->userService->method('getAll')->willReturn([]);
        $this->documentService->method('getAll')->willReturn([]);

        $result = $this->api->getUsersDetailed($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testGetDocumentType()
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $this->userService
            ->method('getDocumentTypes')
            ->willReturn([]);

        $result = $this->api->getDocumentType($request, $response, []);

        $this->assertSame($response, $result);
    }
}