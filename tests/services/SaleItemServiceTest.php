<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\SaleItemService;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Exceptions\NotFoundException;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Repositories\Contracts\SaleItemRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\SaleRepositoryInterface;

class SaleItemServiceTest extends TestCase
{
    private SaleItemRepositoryInterface $repository;
    private ProductRepositoryInterface $productRepository;
    private SaleRepositoryInterface $saleRepository;

    private SaleItemService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(SaleItemRepositoryInterface::class);
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->saleRepository = $this->createMock(SaleRepositoryInterface::class);

        $this->service = new SaleItemService(
            $this->repository,
            $this->productRepository,
            $this->saleRepository
        );
    }

    private function createSale(): Sale
    {
        return new Sale([
            'id' => 1,
            'user_id' => 10
        ]);
    }

    private function createSaleItem(): SaleItem
    {
        return new SaleItem([
            'id' => 1,
            'sale_id' => 1,
            'product_id' => 100,
            'quantity' => 2,
            'price' => 50
        ]);
    }

    public function testGetAllReturnsItems(): void
    {
        $this->repository
            ->method('findAll')
            ->willReturn([$this->createSaleItem()]);

        $result = $this->service->getAll();

        $this->assertCount(1, $result);
    }

    public function testGetSaleThrowsWhenNotFound(): void
    {
        $this->saleRepository
            ->method('findById')
            ->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->getSale(1);
    }

    public function testGetSaleReturnsSale(): void
    {
        $this->saleRepository
            ->method('findById')
            ->willReturn($this->createSale());

        $result = $this->service->getSale(1);

        $this->assertInstanceOf(Sale::class, $result);
    }

    public function testSaveThrowsWhenSaleNotFound(): void
    {
        $this->saleRepository
            ->method('findById')
            ->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->save([
            'sale_id' => 1,
            'product_id' => 100
        ]);
    }

    public function testSaveThrowsWhenProductNotFound(): void
    {
        $this->saleRepository
            ->method('findById')
            ->willReturn($this->createSale());

        $this->productRepository
            ->method('findById')
            ->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->save([
            'sale_id' => 1,
            'product_id' => 100
        ]);
    }

    public function testSaveReturnsSaleItem(): void
    {
        $saleItem = new \App\Models\SaleItem([
            'id' => 1,
            'sale_id' => 1,
            'product_id' => 100,
            'quantity' => 2,
            'price' => 50
        ]);

        $this->saleRepository
            ->method('findById')
            ->willReturn($this->createSale());

        $this->productRepository
            ->method('findById')
            ->willReturn($this->createMock(\App\Models\Product::class));

        $this->repository
            ->method('insert')
            ->willReturn($saleItem);

        $result = $this->service->save([
            'sale_id' => 1,
            'product_id' => 100,
            'quantity' => 2,
            'price' => 50
        ]);

        $this->assertInstanceOf(\App\Models\SaleItem::class, $result);
    }

    public function testUpdateThrowsWhenNotFound(): void
    {
        $this->repository
            ->method('findById')
            ->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->update([
            'id' => 1,
            'sale_id' => 1,
            'product_id' => 100
        ]);
    }

    public function testUpdateThrowsWhenProductNotFound(): void
    {
        $this->repository
            ->method('findById')
            ->willReturn($this->createSaleItem());

        $this->saleRepository
            ->method('findById')
            ->willReturn($this->createSale());

        $this->productRepository
            ->method('findById')
            ->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->update([
            'id' => 1,
            'sale_id' => 1,
            'product_id' => 100
        ]);
    }

    public function testUpdateReturnsSaleItem(): void
    {
        $saleItem = new \App\Models\SaleItem([
            'id' => 1,
            'sale_id' => 1,
            'product_id' => 100,
            'quantity' => 2,
            'price' => 50
        ]);

        $this->repository
            ->method('findById')
            ->willReturn($saleItem);

        $this->saleRepository
            ->method('findById')
            ->willReturn($this->createSale());

        $this->productRepository
            ->method('findById')
            ->willReturn($this->createMock(\App\Models\Product::class));

        $this->repository
            ->method('update')
            ->willReturn($saleItem);

        $result = $this->service->update([
            'id' => 1,
            'sale_id' => 1,
            'product_id' => 100,
            'quantity' => 2,
            'price' => 50
        ]);

        $this->assertInstanceOf(\App\Models\SaleItem::class, $result);
    }

    public function testDeleteSuccess(): void
    {
        $this->repository
            ->method('findById')
            ->willReturn($this->createSaleItem());

        $this->repository
            ->method('deleteByIdAndSaleId')
            ->willReturn(true);

        $this->service->deleteItemById(1, 1);

        $this->assertTrue(true);
    }
}
