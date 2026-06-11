<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\SaleService;
use App\Models\Sale;
use App\Models\User;
use App\Models\SaleItem;
use App\Exceptions\NotFoundException;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Repositories\Contracts\SaleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\SaleItemRepositoryInterface;
use App\Dtos\SaleDetailedDTO;
use App\Dtos\SaleDetailedUsernameDTO;

class SaleServiceTest extends TestCase
{
    private SaleRepositoryInterface $repository;
    private UserRepositoryInterface $userRepository;
    private ProductRepositoryInterface $productRepository;
    private SaleItemRepositoryInterface $saleItemRepository;

    private SaleService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(SaleRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->saleItemRepository = $this->createMock(SaleItemRepositoryInterface::class);

        $this->service = new SaleService(
            $this->repository,
            $this->userRepository,
            $this->productRepository,
            $this->saleItemRepository
        );
    }

    private function createSale(): Sale
    {
        return new Sale([
            'id' => 1,
            'user_id' => 10,
            'total_amount' => 100
        ]);
    }

    private function createUser(): User
    {
        return new User([
            'id' => 10,
            'username' => 'john'
        ]);
    }

    public function testGetSaleThrowsExceptionWhenNotFound(): void
    {
        $this->repository
            ->method('findById')
            ->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->getSale(1);
    }

    public function testGetSaleReturnsSale(): void
    {
        $sale = $this->createSale();

        $this->repository
            ->method('findById')
            ->willReturn($sale);

        $result = $this->service->getSale(1);

        $this->assertSame($sale, $result);
    }

    public function testSaveThrowsWhenUserNotFound(): void
    {
        $this->userRepository
            ->method('findById')
            ->willReturn(new User(['id' => 10]));

        $this->repository
            ->method('insert')
            ->willThrowException(new \Exception('DB error'));

        $this->expectException(InsertException::class);

        $this->service->save([
            'user_id' => 10
        ]);
    }

    public function testSaveThrowsWhenInsertFails(): void
    {
        $this->userRepository
            ->method('findById')
            ->willReturn(new User(['id' => 10]));

        $this->repository
            ->method('insert')
            ->willThrowException(new \Exception('DB error'));

        $this->expectException(InsertException::class);

        $this->service->save([
            'user_id' => 10
        ]);
    }

    public function testSaveReturnsSale(): void
    {
        $this->userRepository
            ->method('findById')
            ->willReturn(new User(['id' => 10]));

        $this->repository
            ->method('insert')
            ->willReturn(new Sale([
                'id' => 1,
                'user_id' => 10
            ]));

        $result = $this->service->save([
            'user_id' => 10,
            'total_amount' => 50
        ]);

        $this->assertInstanceOf(Sale::class, $result);
    }

    public function testUpdateReturnsSale(): void
    {
        $sale = $this->createSale();

        $this->repository
            ->method('findById')
            ->willReturn($sale);

        $this->repository
            ->method('update')
            ->willReturn($sale);

        $result = $this->service->update([
            'id' => 1,
            'user_id' => 10,
            'total_amount' => 200.0
        ]);

        $this->assertInstanceOf(Sale::class, $result);
        $this->assertSame(200.0, $result->getTotalAmount());
        $this->assertSame(10, $result->getUserId());
    }

    public function testUpdateThrowsExceptionWhenFails(): void
    {
        $sale = $this->createSale();

        $this->repository
            ->method('findById')
            ->willReturn($sale);

        $this->repository
            ->method('update')
            ->willThrowException(new \Exception('DB error'));

        $this->expectException(UpdateException::class);

        $this->service->update([
            'id' => 1,
            'user_id' => 10
        ]);
    }

    public function testDeleteSaleThrowsWhenNotFound(): void
    {
        $this->repository
            ->method('findById')
            ->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->deleteSale(1);
    }

    public function testDeleteSaleThrowsWhenDeleteFails(): void
    {
        $sale = $this->createSale();

        $this->repository
            ->method('findById')
            ->willReturn($sale);

        $this->saleItemRepository
            ->method('findBySale')
            ->willReturn([]);

        $this->repository
            ->method('delete')
            ->willReturn(false);

        $this->expectException(DeleteException::class);

        $this->service->deleteSale(1);
    }

    public function testDeleteSaleSucceeds(): void
    {
        $sale = $this->createSale();

        $item = $this->createMock(SaleItem::class);
        $item->method('getId')->willReturn(1);

        $this->repository
            ->method('findById')
            ->willReturn($sale);

        $this->saleItemRepository
            ->method('findBySale')
            ->willReturn([$item]);

        $this->saleItemRepository
            ->method('delete')
            ->willReturn(true);

        $this->repository
            ->method('delete')
            ->willReturn(true);

        $this->service->deleteSale(1);

        $this->assertTrue(true);
    }

    public function testGetSalesReturnsRepositoryData(): void
    {
        $this->repository
            ->method('findAll')
            ->willReturn([$this->createSale()]);

        $result = $this->service->getSales();

        $this->assertCount(1, $result);
    }

    public function testGetSalesByUserIdReturnsData(): void
    {
        $this->repository
            ->method('findByUserId')
            ->willReturn([$this->createSale()]);

        $result = $this->service->getSalesByUserId(10);

        $this->assertCount(1, $result);
    }

    public function testGetSalesByProductReturnsData(): void
    {
        $this->repository
            ->method('findByProductId')
            ->willReturn([$this->createSale()]);

        $result = $this->service->getSalesByProduct(1);

        $this->assertCount(1, $result);
    }

    public function testGetPurchasesByUserReturnsDTO(): void
    {
        $sale = $this->createSale();

        $item = $this->createMock(SaleItem::class);
        $item->method('toArray')->willReturn(['id' => 1]);
        $item->method('getProductId')->willReturn(100);

        $this->repository
            ->method('findByUserId')
            ->willReturn([$sale]);

        $this->saleItemRepository
            ->method('findBySale')
            ->willReturn([$item]);

        $result = $this->service->getPurchasesByUser(10);

        $this->assertInstanceOf(SaleDetailedDTO::class, $result[0]);
    }

    public function testGetSalesWithUserAndItemsReturnsDTO(): void
    {
        $sale = $this->createSale();
        $user = $this->createUser();

        $item = $this->createMock(SaleItem::class);
        $item->method('toArray')->willReturn(['id' => 1]);
        $item->method('getProductId')->willReturn(100);

        $product = new \App\Models\Product([
            'id' => 100,
            'name' => 'Product A'
        ]);

        $this->repository
            ->method('findAll')
            ->willReturn([$sale]);

        $this->userRepository
            ->method('findById')
            ->willReturn($user);

        $this->saleItemRepository
            ->method('findBySale')
            ->willReturn([$item]);

        $this->productRepository
            ->method('findById')
            ->willReturn($product);

        $result = $this->service->getSalesWithUserAndItems();

        $this->assertInstanceOf(SaleDetailedUsernameDTO::class, $result[0]);
    }

    public function testGetSalesDetailedReturnsRepository(): void
    {
        $this->repository
            ->method('findAllDetailed')
            ->willReturn([]);

        $result = $this->service->getSalesDetailed();

        $this->assertIsArray($result);
    }
}
