<?php

namespace Tests\Unit\Api;

use PHPUnit\Framework\TestCase;
use App\Api\CategoryApi;
use App\Services\CategoryService;
use Rakit\Validation\Validator;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use App\Models\Category;

class CategoryApiTest extends TestCase
{
    private $service;
    private $validator;
    private $api;

    protected function setUp(): void
    {
        $this->service = $this->createMock(CategoryService::class);
        $this->validator = new Validator();

        $this->api = new CategoryApi($this->service, $this->validator);
    }

    private function createStream(string $content = '')
    {
        return new class($content) implements StreamInterface {
            private string $content;

            public function __construct(string $content)
            {
                $this->content = $content;
            }

            public function write(string $string): int
            {
                $this->content .= $string;
                return strlen($string);
            }

            public function getContents(): string
            {
                return $this->content;
            }

            public function __toString(): string
            {
                return $this->content;
            }

            public function close(): void {}
            public function detach() {}
            public function eof(): bool
            {
                return false;
            }
            public function tell(): int
            {
                return 0;
            }
            public function isSeekable(): bool
            {
                return false;
            }
            public function seek($offset, $whence = SEEK_SET): void {}
            public function rewind(): void {}
            public function isWritable(): bool
            {
                return true;
            }
            public function isReadable(): bool
            {
                return true;
            }
            public function getSize(): ?int
            {
                return strlen($this->content);
            }
            public function read($length): string
            {
                return '';
            }
            public function getMetadata($key = null)
            {
                return null;
            }
        };
    }

    private function createResponse()
    {
        $body = $this->createStream('');

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($body);
        $response->method('withStatus')->willReturnSelf();
        $response->method('withHeader')->willReturnSelf();

        return $response;
    }

    public function testGetAllReturnsCategories()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn([]);

        $response = $this->createResponse();

        $this->service->method('getAll')->willReturn([
            ['id' => 1, 'name' => 'Cat 1']
        ]);

        $result = $this->api->getAll($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testGetAllWithPagination()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn([
            'start' => 0,
            'length' => 10,
            'draw' => 1
        ]);

        $response = $this->createResponse();

        $this->service->method('paginate')->willReturn([
            'recordsTotal' => 1,
            'recordsFiltered' => 1,
            'data' => []
        ]);

        $result = $this->api->getAll($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testGetCategoryById()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createResponse();

        $this->service->method('getCategory')->willReturn([
            'id' => 1,
            'name' => 'Cat'
        ]);

        $result = $this->api->getCategoryById($request, $response, ['id' => 1]);

        $this->assertSame($response, $result);
    }

    public function testSaveCategory()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')->willReturn(
            $this->createStream(json_encode(['name' => 'New Cat']))
        );
        $request->method('getMethod')->willReturn('POST');

        $response = $this->createResponse();

        $this->service->method('save')->willReturn([
            'id' => 1,
            'name' => 'New Cat'
        ]);

        $result = $this->api->save($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testSaveInvalidJson()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')->willReturn(
            $this->createStream('INVALID JSON')
        );

        $response = $this->createResponse();

        $result = $this->api->save($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testDeleteCategory()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createResponse();

        $this->service->expects($this->once())
            ->method('delete');

        $result = $this->api->delete($request, $response, ['id' => 1]);

        $this->assertSame($response, $result);
    }

    public function testSaveInvalidData()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')->willReturn(
            $this->createStream(json_encode([
                'name' => 'ab'
            ]))
        );
        $request->method('getMethod')->willReturn('POST');

        $response = $this->createResponse();

        $result = $this->api->save($request, $response, []);

        $this->assertSame($response, $result);
    }


    public function testUpdateCategory()
    {
        $request = $this->createMock(ServerRequestInterface::class);

        $request->method('getBody')->willReturn(
            $this->createStream(json_encode([
                'name' => 'Updated Category'
            ]))
        );

        $request->method('getMethod')->willReturn('PUT');

        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('update')
            ->with([
                'id' => 1,
                'name' => 'Updated Category'
            ])
            ->willReturn([
                'id' => 1,
                'name' => 'Updated Category'
            ]);

        $result = $this->api->save($request, $response, ['id' => 1]);

        $this->assertSame($response, $result);
    }

    public function testSaveInvalidJsonDoesNotCallService()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')
            ->willReturn($this->createStream('INVALID JSON'));

        $response = $this->createResponse();

        $this->service
            ->expects($this->never())
            ->method('save');

        $this->api->save($request, $response, []);
    }


    public function testUpdateInvalidDataDoesNotCallService()
    {
        $request = $this->createMock(ServerRequestInterface::class);

        $request->method('getMethod')
            ->willReturn('PUT');

        $request->method('getBody')
            ->willReturn(
                $this->createStream(json_encode([
                    'name' => 'ab'
                ]))
            );

        $response = $this->createResponse();

        $this->service
            ->expects($this->never())
            ->method('update');

        $this->api->save($request, $response, ['id' => 1]);
    }

    public function testGetAllPaginationPassesCorrectParams()
    {
        $params = [
            'start' => 0,
            'length' => 10,
            'draw' => 1
        ];

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')
            ->willReturn($params);

        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('paginate')
            ->with($params)
            ->willReturn([
                'recordsTotal' => 1,
                'recordsFiltered' => 1,
                'data' => []
            ]);

        $this->api->getAll($request, $response, []);
    }

    public function testGetCategoryByIdPassesCorrectId()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('getCategory')
            ->with(1)
            ->willReturn([
                'id' => 1,
                'name' => 'Category'
            ]);

        $this->api->getCategoryById($request, $response, ['id' => 1]);
    }

    public function testSavePassesCorrectDataToService()
    {
        $payload = [
            'name' => 'New Category'
        ];

        $request = $this->createMock(ServerRequestInterface::class);

        $request->method('getMethod')
            ->willReturn('POST');

        $request->method('getBody')
            ->willReturn(
                $this->createStream(json_encode($payload))
            );

        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('save')
            ->with($payload)
            ->willReturn([
                'id' => 1,
                'name' => 'New Category'
            ]);

        $this->api->save($request, $response, []);
    }


    public function testGetCategoriesName()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createResponse();

        $category = $this->createMock(Category::class);

        $category->method('getId')
            ->willReturn(1);

        $category->method('getName')
            ->willReturn('Category 1');

        $this->service
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([$category]);

        $result = $this->api->getCategoriesName(
            $request,
            $response,
            []
        );

        $this->assertSame($response, $result);
    }

    public function testSaveFailsWhenDescriptionTooShort()
    {
        $request = $this->createMock(ServerRequestInterface::class);

        $request->method('getMethod')
            ->willReturn('POST');

        $request->method('getBody')
            ->willReturn(
                $this->createStream(json_encode([
                    'name' => 'Valid Category',
                    'description' => 'ab'
                ]))
            );

        $response = $this->createResponse();

        $this->service
            ->expects($this->never())
            ->method('save');

        $this->api->save($request, $response, []);
    }

    public function testSaveFailsWhenDescriptionTooLong()
    {
        $request = $this->createMock(ServerRequestInterface::class);

        $request->method('getMethod')
            ->willReturn('POST');

        $request->method('getBody')
            ->willReturn(
                $this->createStream(json_encode([
                    'name' => 'Valid Category',
                    'description' => str_repeat('a', 256)
                ]))
            );

        $response = $this->createResponse();

        $this->service
            ->expects($this->never())
            ->method('save');

        $this->api->save($request, $response, []);
    }

    public function testSaveFailsWhenActiveIsNotBoolean()
    {
        $request = $this->createMock(ServerRequestInterface::class);

        $request->method('getMethod')
            ->willReturn('POST');

        $request->method('getBody')
            ->willReturn(
                $this->createStream(json_encode([
                    'name' => 'Valid Category',
                    'active' => 'invalid'
                ]))
            );

        $response = $this->createResponse();

        $this->service
            ->expects($this->never())
            ->method('save');

        $this->api->save($request, $response, []);
    }

    public function testUpdatePassesCorrectDataToService()
    {
        $request = $this->createMock(ServerRequestInterface::class);

        $request->method('getMethod')
            ->willReturn('PUT');

        $request->method('getBody')
            ->willReturn(
                $this->createStream(json_encode([
                    'name' => 'Updated Category'
                ]))
            );

        $response = $this->createResponse();

        $expected = [
            'id' => 5,
            'name' => 'Updated Category'
        ];

        $this->service
            ->expects($this->once())
            ->method('update')
            ->with($expected)
            ->willReturn($expected);

        $this->api->save($request, $response, ['id' => 5]);
    }
}
