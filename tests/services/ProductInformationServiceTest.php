<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\ProductInformationService;
use App\Models\ProductInformation;
use App\Models\Product;
use App\Exceptions\NotFoundException;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\DuplicateException;
use App\Repositories\Contracts\ProductInformationRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductInformationServiceTest extends TestCase
{
    private $repository;
    private $productRepository;
    private ProductInformationService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(ProductInformationRepositoryInterface::class);
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);

        $this->service = new ProductInformationService(
            $this->repository,
            $this->productRepository
        );
    }

    private function mockProduct(int $id = 1)
    {
        $product = $this->createMock(Product::class);
        $product->method('getId')->willReturn($id);

        return $product;
    }

    private function mockInfo(int $id = 1)
    {
        $info = $this->createMock(ProductInformation::class);
        $info->method('getId')->willReturn($id);

        return $info;
    }

    public function testGetByIdThrowsNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->getById(1);
    }

    public function testGetByIdSuccess(): void
    {
        $info = $this->mockInfo();

        $this->repository->method('findById')->willReturn($info);

        $result = $this->service->getById(1);

        $this->assertSame($info, $result);
    }

    public function testSaveThrowsProductNotFound(): void
    {
        $this->productRepository->method('findById')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->save([
            'product_id' => 1
        ]);
    }

    public function testSaveThrowsDuplicate(): void
    {
        $this->productRepository->method('findById')->willReturn($this->mockProduct());
        $this->repository->method('findByProductId')->willReturn($this->mockInfo());

        $this->expectException(DuplicateException::class);

        $this->service->save([
            'product_id' => 1
        ]);
    }

    public function testSaveSuccess(): void
    {
        $this->productRepository->method('findById')->willReturn($this->mockProduct());
        $this->repository->method('findByProductId')->willReturn(null);

        $info = $this->mockInfo();

        $this->repository
            ->method('insert')
            ->willReturn($info);

        $result = $this->service->save([
            'product_id' => 1
        ]);

        $this->assertSame($info, $result);
    }

    public function testSaveThrowsInsertException(): void
    {
        $this->productRepository->method('findById')->willReturn($this->mockProduct());
        $this->repository->method('findByProductId')->willReturn(null);

        $this->repository
            ->method('insert')
            ->willThrowException(new \Exception());

        $this->expectException(InsertException::class);

        $this->service->save([
            'product_id' => 1
        ]);
    }

    public function testUpdateThrowsNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->update([
            'id' => 1
        ]);
    }

    public function testUpdateSuccess(): void
    {
        $info = $this->mockInfo();

        $this->repository->method('findById')->willReturn($info);

        $this->repository
            ->method('update')
            ->willReturn($info);

        $result = $this->service->update([
            'id' => 1,
            'brand' => 'test'
        ]);

        $this->assertSame($info, $result);
    }

    public function testUpdateThrowsUpdateException(): void
    {
        $info = $this->mockInfo();

        $this->repository->method('findById')->willReturn($info);

        $this->repository
            ->method('update')
            ->willThrowException(new \Exception());

        $this->expectException(UpdateException::class);

        $this->service->update([
            'id' => 1,
            'brand' => 'test'
        ]);
    }

    public function testDeleteSuccess(): void
    {
        $info = $this->mockInfo();

        $this->repository->method('findById')->willReturn($info);
        $this->repository->method('delete')->willReturn(true);

        $this->service->delete(1);

        $this->assertTrue(true);
    }

    public function testDeleteThrowsException(): void
    {
        $info = $this->mockInfo();

        $this->repository->method('findById')->willReturn($info);
        $this->repository->method('delete')->willReturn(false);

        $this->expectException(DeleteException::class);

        $this->service->delete(1);
    }
}
