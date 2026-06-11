<?php

namespace Tests\Unit\Api;

use PHPUnit\Framework\TestCase;
use App\Api\ReviewApi;
use App\Services\ReviewService;
use Rakit\Validation\Validator;
use Rakit\Validation\ErrorBag;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ReviewApiTest extends TestCase
{
    private ReviewService $service;
    private Validator $validator;
    private ReviewApi $api;

    protected function setUp(): void
    {
        $this->service = $this->createMock(ReviewService::class);
        $this->validator = $this->createMock(Validator::class);

        $this->api = new ReviewApi($this->service, $this->validator);
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

    /* =========================
        GET TESTS
    ========================== */

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

    public function testGetDetailedReviews()
    {
        $request = $this->createRequest();
        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('getAll')
            ->willReturn([]);

        $result = $this->api->getDetailedReviews($request, $response, []);

        $this->assertSame($response, $result);
    }

    public function testGetReviewById()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('getReview')
            ->with(5)
            ->willReturn([]);

        $result = $this->api->getReviewById(
            $request,
            $response,
            ['id' => 5]
        );

        $this->assertSame($response, $result);
    }

    public function testGetUserReviews()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('getReviewsByUser')
            ->with(1)
            ->willReturn([]);

        $result = $this->api->getUserReviews(
            $request,
            $response,
            ['id' => 1]
        );

        $this->assertSame($response, $result);
    }

    public function testGetProductReviews()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createResponse();

        $this->service
            ->expects($this->once())
            ->method('getReviewsByProduct')
            ->with(2)
            ->willReturn([]);

        $result = $this->api->getProductReviews(
            $request,
            $response,
            ['id' => 2]
        );

        $this->assertSame($response, $result);
    }

    /* =========================
        SAVE / UPDATE
    ========================== */

    public function testSaveSuccess()
    {
        $request = $this->createRequest(
            [],
            json_encode([
                'product_id' => 1,
                'user_id' => 1,
                'rating' => 4.5
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
        $errors->method('toArray')->willReturn([
            'product_id' => ['required']
        ]);

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
                'user_id' => 1,
                'rating' => 5
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

        $result = $this->api->save($request, $response, ['id' => 1]);

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

        $result = $this->api->delete($request, $response, ['id' => 1]);

        $this->assertSame($response, $result);
    }

    public function testDeleteReviewsByUser()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createResponse();

        $review1 = new class {
            public function getId(): int
            {
                return 10;
            }
        };

        $review2 = new class {
            public function getId(): int
            {
                return 20;
            }
        };

        $this->service
            ->expects($this->once())
            ->method('getReviewsByUser')
            ->with(5)
            ->willReturn([$review1, $review2]);

        $this->service
            ->expects($this->exactly(2))
            ->method('delete');

        $result = $this->api->deleteReviewsbyUser(
            $request,
            $response,
            ['id' => 5]
        );

        $this->assertSame($response, $result);
    }

    public function testDeleteReviewsByProduct()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createResponse();

        $review1 = new class {
            public function getId(): int
            {
                return 30;
            }
        };

        $review2 = new class {
            public function getId(): int
            {
                return 40;
            }
        };

        $this->service
            ->expects($this->once())
            ->method('getReviewsByProduct')
            ->with(10)
            ->willReturn([$review1, $review2]);

        $this->service
            ->expects($this->exactly(2))
            ->method('delete');

        $result = $this->api->deleteReviewsbyProduct(
            $request,
            $response,
            ['id' => 10]
        );

        $this->assertSame($response, $result);
    }
}
