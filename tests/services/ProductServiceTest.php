<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\ProductService;
use App\Models\Product;
use App\Exceptions\DuplicateException;
use App\Exceptions\NotFoundException;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\ForeignKeyException;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class ProductServiceTest extends TestCase
{
    private $repository;
    private $categoryRepository;
    private ProductService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(ProductRepositoryInterface::class);
        $this->categoryRepository = $this->createMock(CategoryRepositoryInterface::class);

        $this->service = new ProductService(
            $this->repository,
            $this->categoryRepository
        );
    }

    private function mockProduct(int $id = 1): Product
    {
        $product = $this->createMock(Product::class);
        $product->method('getId')->willReturn($id);
        return $product;
    }

    private function mockCategory(int $id = 1)
    {
        $category = $this->createMock(\App\Models\Category::class);
        $category->method('getId')->willReturn($id);

        return $category;
    }

    public function testGetProductThrowsNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->getProduct(1);
    }
    public function testGetProductReturnsProduct(): void
    {
        $product = $this->mockProduct();

        $this->repository->method('findById')->willReturn($product);

        $result = $this->service->getProduct(1);

        $this->assertSame($product, $result);
    }
    public function testSaveThrowsDuplicateName(): void
    {
        $this->repository->method('findByName')->willReturn($this->mockProduct());

        $this->expectException(DuplicateException::class);

        $this->service->save([
            'name' => 'p',
            'code' => '123',
            'category_id' => 1
        ]);
    }
    public function testSaveThrowsDuplicateCode(): void
    {
        $this->repository->method('findByName')->willReturn(null);
        $this->repository->method('findByCode')->willReturn($this->mockProduct());

        $this->expectException(DuplicateException::class);

        $this->service->save([
            'name' => 'p',
            'code' => '123',
            'category_id' => 1
        ]);
    }
    public function testSaveThrowsCategoryNotFound(): void
    {
        $this->repository->method('findByName')->willReturn(null);
        $this->repository->method('findByCode')->willReturn(null);
        $this->categoryRepository->method('findById')->willReturn(null);

        $this->expectException(InsertException::class);

        $this->service->save([
            'name' => 'p',
            'code' => '123',
            'category_id' => 1
        ]);
    }
    public function testSaveSuccess(): void
    {
        $this->repository->method('findByName')->willReturn(null);
        $this->repository->method('findByCode')->willReturn(null);
        $this->categoryRepository
            ->method('findById')
            ->willReturn($this->mockCategory());

        $product = $this->mockProduct();

        $this->repository
            ->method('insert')
            ->willReturn($product);

        $result = $this->service->save([
            'name' => 'p',
            'code' => '123',
            'category_id' => 1
        ]);

        $this->assertSame($product, $result);
    }
    public function testSaveThrowsInsertException(): void
    {
        $this->repository->method('findByName')->willReturn(null);
        $this->repository->method('findByCode')->willReturn(null);
        $this->categoryRepository
            ->method('findById')
            ->willReturn($this->mockCategory());

        $this->repository
            ->method('insert')
            ->willThrowException(new \Exception());

        $this->expectException(InsertException::class);

        $this->service->save([
            'name' => 'p',
            'code' => '123',
            'category_id' => 1
        ]);
    }
    public function testUpdateThrowsNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->update([
            'id' => 1,
            'name' => 'p',
            'code' => '123',
            'category_id' => 1
        ]);
    }
    public function testUpdateThrowsDuplicateName(): void
    {
        $product = $this->mockProduct(1);
        $other = $this->mockProduct(2);

        $this->repository->method('findById')->willReturn($product);
        $this->repository->method('findByName')->willReturn($other);

        $this->expectException(DuplicateException::class);

        $this->service->update([
            'id' => 1,
            'name' => 'p',
            'code' => '123',
            'category_id' => 1
        ]);
    }
    public function testUpdateThrowsDuplicateCode(): void
    {
        $product = $this->mockProduct(1);
        $other = $this->mockProduct(2);

        $this->repository->method('findById')->willReturn($product);
        $this->repository->method('findByName')->willReturn(null);
        $this->repository->method('findByCode')->willReturn($other);

        $this->expectException(DuplicateException::class);

        $this->service->update([
            'id' => 1,
            'name' => 'p',
            'code' => '123',
            'category_id' => 1
        ]);
    }
    public function testUpdateThrowsForeignKey(): void
    {
        $product = $this->mockProduct(1);

        $this->repository->method('findById')->willReturn($product);
        $this->repository->method('findByName')->willReturn(null);
        $this->repository->method('findByCode')->willReturn(null);
        $this->categoryRepository->method('findById')->willReturn(null);

        $this->expectException(ForeignKeyException::class);

        $this->service->update([
            'id' => 1,
            'name' => 'p',
            'code' => '123',
            'category_id' => 1
        ]);
    }
    public function testUpdateSuccess(): void
    {
        $product = $this->mockProduct(1);

        $this->repository->method('findById')->willReturn($product);
        $this->repository->method('findByName')->willReturn(null);
        $this->repository->method('findByCode')->willReturn(null);
        $this->categoryRepository
            ->method('findById')
            ->willReturn($this->mockCategory());

        $this->repository
            ->method('update')
            ->willReturn($product);

        $result = $this->service->update([
            'id' => 1,
            'name' => 'p',
            'code' => '123',
            'category_id' => 1
        ]);

        $this->assertSame($product, $result);
    }
    public function testDeleteSuccess(): void
    {
        $product = $this->mockProduct(1);

        $this->repository->method('findById')->willReturn($product);
        $this->repository->method('delete')->willReturn(true);

        $this->service->delete(1);

        $this->assertTrue(true);
    }

    public function testDeleteThrowsException(): void
    {
        $product = $this->mockProduct(1);

        $this->repository->method('findById')->willReturn($product);
        $this->repository->method('delete')->willReturn(false);

        $this->expectException(DeleteException::class);

        $this->service->delete(1);
    }
}
