<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\ReviewService;
use App\Models\Review;
use App\Models\User;
use App\Models\Product;
use App\Exceptions\NotFoundException;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\DuplicateException;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ReviewServiceTest extends TestCase
{
    private $repository;
    private $userRepository;
    private $productRepository;
    private ReviewService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(ReviewRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);

        $this->service = new ReviewService(
            $this->repository,
            $this->userRepository,
            $this->productRepository
        );
    }

    private function mockReview(int $id = 1)
    {
        $review = $this->createMock(Review::class);
        $review->method('getId')->willReturn($id);
        $review->method('getActive')->willReturn(true);
        $review->method('getRating')->willReturn(5);

        return $review;
    }

    private function mockUser()
    {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(1);

        return $user;
    }

    private function mockProduct()
    {
        $product = $this->createMock(Product::class);
        $product->method('getId')->willReturn(1);

        return $product;
    }

    public function testGetReviewThrowsNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->getReview(1);
    }

    public function testGetReviewSuccess(): void
    {
        $review = $this->mockReview();

        $this->repository->method('findById')->willReturn($review);

        $result = $this->service->getReview(1);

        $this->assertSame($review, $result);
    }

    public function testSaveThrowsUserNotFound(): void
    {
        $this->userRepository->method('findById')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->save([
            'user_id' => 1,
            'product_id' => 1
        ]);
    }

    public function testSaveThrowsProductNotFound(): void
    {
        $this->userRepository->method('findById')->willReturn($this->mockUser());
        $this->productRepository->method('findById')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->save([
            'user_id' => 1,
            'product_id' => 1
        ]);
    }


    public function testSaveThrowsDuplicate(): void
    {
        $this->userRepository->method('findById')->willReturn($this->mockUser());
        $this->productRepository->method('findById')->willReturn($this->mockProduct());
        $this->repository->method('findByProductIdAndUserId')->willReturn($this->mockReview());

        $this->expectException(DuplicateException::class);

        $this->service->save([
            'user_id' => 1,
            'product_id' => 1
        ]);
    }

    public function testSaveSuccess(): void
    {
        $this->userRepository->method('findById')->willReturn($this->mockUser());
        $this->productRepository->method('findById')->willReturn($this->mockProduct());
        $this->repository->method('findByProductIdAndUserId')->willReturn(null);

        $review = $this->mockReview();

        $this->repository
            ->method('insert')
            ->willReturn($review);

        $result = $this->service->save([
            'user_id' => 1,
            'product_id' => 1,
            'rating' => 5
        ]);

        $this->assertSame($review, $result);
    }

    public function testSaveThrowsInsertException(): void
    {
        $this->userRepository->method('findById')->willReturn($this->mockUser());
        $this->productRepository->method('findById')->willReturn($this->mockProduct());
        $this->repository->method('findByProductIdAndUserId')->willReturn(null);

        $this->repository
            ->method('insert')
            ->willThrowException(new \Exception());

        $this->expectException(InsertException::class);

        $this->service->save([
            'user_id' => 1,
            'product_id' => 1
        ]);
    }

    public function testUpdateThrowsNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->update([
            'id' => 1,
            'user_id' => 1,
            'product_id' => 1
        ]);
    }

    public function testUpdateSuccess(): void
    {
        $review = $this->mockReview();

        $this->repository->method('findById')->willReturn($review);
        $this->productRepository->method('findById')->willReturn($this->mockProduct());
        $this->userRepository->method('findById')->willReturn($this->mockUser());

        $this->repository
            ->method('update')
            ->willReturn($review);

        $result = $this->service->update([
            'id' => 1,
            'user_id' => 1,
            'product_id' => 1,
            'rating' => 4
        ]);

        $this->assertSame($review, $result);
    }

    public function testUpdateThrowsUpdateException(): void
    {
        $review = $this->mockReview();

        $this->repository->method('findById')->willReturn($review);
        $this->productRepository->method('findById')->willReturn($this->mockProduct());
        $this->userRepository->method('findById')->willReturn($this->mockUser());

        $this->repository
            ->method('update')
            ->willThrowException(new \Exception());

        $this->expectException(UpdateException::class);

        $this->service->update([
            'id' => 1,
            'user_id' => 1,
            'product_id' => 1
        ]);
    }

    public function testDeleteSuccess(): void
    {
        $review = $this->mockReview();

        $this->repository->method('findById')->willReturn($review);
        $this->repository->method('delete')->willReturn(true);

        $this->service->delete(1);

        $this->assertTrue(true);
    }

    public function testDeleteThrowsException(): void
    {
        $review = $this->mockReview();

        $this->repository->method('findById')->willReturn($review);
        $this->repository->method('delete')->willReturn(false);

        $this->expectException(DeleteException::class);

        $this->service->delete(1);
    }

    public function testDeleteReviewsByUserSuccess(): void
    {
        $user = $this->mockUser();
        $review = $this->mockReview();

        $this->repository->method('findByUserId')->willReturn([$review]);
        $this->repository->method('delete')->willReturn(true);

        $this->service->deleteReviewsByUser($user);

        $this->assertTrue(true);
    }

    public function testGetProductRatingStatsEmpty(): void
    {
        $this->repository->method('findByProductId')->willReturn([]);

        $result = $this->service->getProductRatingStats(1);

        $this->assertEquals(['average' => 0, 'total' => 0], $result);
    }

    public function testGetProductRatingStatsOk(): void
    {
        $review = $this->mockReview();

        $this->repository->method('findByProductId')->willReturn([$review]);

        $result = $this->service->getProductRatingStats(1);

        $this->assertEquals(['average' => 5.0, 'total' => 1], $result);
    }
}
