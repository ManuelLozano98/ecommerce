<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\CategoryService;
use App\Models\Category;
use App\Models\Product;
use App\Exceptions\NotFoundException;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\DuplicateException;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;

class CategoryServiceTest extends TestCase
{
    private CategoryRepositoryInterface $repository;
    private ProductRepositoryInterface $productRepository;
    private CategoryService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(CategoryRepositoryInterface::class);
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);

        $this->service = new CategoryService(
            $this->repository,
            $this->productRepository
        );
    }

    private function createCategory(): Category
    {
        return new Category([
            'id' => 1,
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Category description',
            'active' => true
        ]);
    }

    public function testGetAllReturnsCategories(): void
    {
        $this->repository
            ->method('findAll')
            ->willReturn([$this->createCategory()]);

        $result = $this->service->getAll();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function testGetCategoryReturnsCategory(): void
    {
        $this->repository
            ->method('findById')
            ->willReturn($this->createCategory());

        $result = $this->service->getCategory(1);

        $this->assertInstanceOf(Category::class, $result);
    }

    public function testGetCategoryThrowsWhenNotFound(): void
    {
        $this->repository
            ->method('findById')
            ->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->getCategory(1);
    }

    public function testGetCategoryByNameReturnsCategory(): void
    {
        $this->repository
            ->method('findByName')
            ->willReturn($this->createCategory());

        $result = $this->service->getCategoryByName('Electronics');

        $this->assertInstanceOf(Category::class, $result);
    }

    public function testGetCategoryByNameThrowsWhenNotFound(): void
    {
        $this->repository
            ->method('findByName')
            ->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->getCategoryByName('Electronics');
    }

    public function testGetActiveReturnsArray(): void
    {
        $this->repository
            ->method('findActive')
            ->willReturn([$this->createCategory()]);

        $result = $this->service->getActive();

        $this->assertIsArray($result);
    }

    public function testGetCategoryByProductReturnsCategory(): void
    {
        $product = $this->createMock(Product::class);

        $this->repository
            ->method('findByProduct')
            ->willReturn($this->createCategory());

        $result = $this->service->getCategoryByProduct($product);

        $this->assertInstanceOf(Category::class, $result);
    }

    public function testGetCategoryBySlugReturnsCategory(): void
    {
        $this->repository
            ->method('findBySlug')
            ->willReturn($this->createCategory());

        $result = $this->service->getCategoryBySlug('electronics');

        $this->assertInstanceOf(Category::class, $result);
    }

    public function testSaveReturnsCategory(): void
    {
        $this->repository
            ->method('findByName')
            ->willReturn(null);

        $this->repository
            ->method('insert')
            ->willReturn($this->createCategory());

        $result = $this->service->save([
            'name' => 'Electronics',
            'slug' => 'electronics'
        ]);

        $this->assertInstanceOf(Category::class, $result);
    }

    public function testSaveThrowsDuplicate(): void
    {
        $this->repository
            ->method('findByName')
            ->willReturn($this->createCategory());

        $this->expectException(DuplicateException::class);

        $this->service->save([
            'name' => 'Electronics'
        ]);
    }

    public function testUpdateReturnsCategory(): void
    {
        $this->repository
            ->method('findById')
            ->willReturn($this->createCategory());

        $this->repository
            ->method('findByName')
            ->willReturn(null);

        $this->repository
            ->method('update')
            ->willReturn($this->createCategory());

        $result = $this->service->update([
            'id' => 1,
            'name' => 'Updated',
            'slug' => 'updated'
        ]);

        $this->assertInstanceOf(Category::class, $result);
    }

    public function testUpdateThrowsDuplicate(): void
    {
        $existing = $this->createCategory();

        $this->repository
            ->method('findById')
            ->willReturn($existing);

        $this->repository
            ->method('findByName')
            ->willReturn(new Category([
                'id' => 2,
                'name' => 'Electronics'
            ]));

        $this->expectException(DuplicateException::class);

        $this->service->update([
            'id' => 1,
            'name' => 'Electronics'
        ]);
    }

    public function testDeleteRemovesCategory(): void
    {
        $this->repository
            ->method('findById')
            ->willReturn($this->createCategory());

        $this->productRepository
            ->method('findByCategory')
            ->willReturn([]);

        $this->productRepository
            ->method('delete')
            ->willReturn(true);

        $this->repository
            ->method('delete')
            ->willReturn(true);

        $this->service->delete(1);

        $this->assertTrue(true);
    }

    public function testDeleteThrowsException(): void
    {
        $this->repository
            ->method('findById')
            ->willReturn($this->createCategory());

        $this->productRepository
            ->method('findByCategory')
            ->willReturn([]);

        $this->repository
            ->method('delete')
            ->willReturn(false);

        $this->expectException(DeleteException::class);

        $this->service->delete(1);
    }

    public function testPaginateReturnsData(): void
    {
        $payload = [
            'data' => [],
            'recordsTotal' => 0
        ];

        $this->repository
            ->method('paginate')
            ->willReturn($payload);

        $result = $this->service->paginate(['draw' => 1]);

        $this->assertSame($payload, $result);
    }
}