<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\ShippingAddressService;
use App\Models\ShippingAddress;
use App\Exceptions\NotFoundException;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\DuplicateException;
use App\Repositories\Contracts\ShippingAddressRepositoryInterface;

class ShippingAddressServiceTest extends TestCase
{
    private ShippingAddressRepositoryInterface $repository;
    private ShippingAddressService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(ShippingAddressRepositoryInterface::class);

        $this->service = new ShippingAddressService(
            $this->repository
        );
    }

    private function createAddress(): ShippingAddress
    {
        return new ShippingAddress([
            'id' => 1,
            'user_id' => 10,
            'full_name' => 'John Doe',
            'phone' => '123456789',
            'address' => 'Street 1',
            'city' => 'Madrid',
            'province' => 'Madrid',
            'country' => 'ES',
            'postal_code' => '28001'
        ]);
    }


    public function testGetAllReturnsAddresses(): void
    {
        $this->repository
            ->method('findAll')
            ->willReturn([$this->createAddress()]);

        $result = $this->service->getAll();

        $this->assertCount(1, $result);
    }

    public function testGetThrowsWhenNotFound(): void
    {
        $this->repository
            ->method('findById')
            ->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->get(1);
    }

    public function testGetReturnsAddress(): void
    {
        $this->repository
            ->method('findById')
            ->willReturn($this->createAddress());

        $result = $this->service->get(1);

        $this->assertInstanceOf(ShippingAddress::class, $result);
    }


    public function testGetByUserReturnsAddress(): void
    {
        $this->repository
            ->method('findByUserId')
            ->willReturn($this->createAddress());

        $result = $this->service->getByUser(10);

        $this->assertInstanceOf(ShippingAddress::class, $result);
    }


    public function testSaveThrowsWhenDuplicate(): void
    {
        $this->repository
            ->method('findByUserId')
            ->willReturn($this->createAddress());

        $this->expectException(DuplicateException::class);

        $this->service->save([
            'user_id' => 10,
            'full_name' => 'John Doe'
        ]);
    }

    public function testSaveReturnsAddress(): void
    {
        $this->repository
            ->method('findByUserId')
            ->willReturn(null);

        $this->repository
            ->method('insert')
            ->willReturn($this->createAddress());

        $result = $this->service->save([
            'user_id' => 10,
            'full_name' => 'John Doe',
            'address' => 'Street 1'
        ]);

        $this->assertInstanceOf(ShippingAddress::class, $result);
    }

    public function testUpdateThrowsWhenNotFound(): void
    {
        $this->repository
            ->method('findById')
            ->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->update([
            'id' => 1,
            'user_id' => 10
        ]);
    }

    public function testUpdateReturnsAddress(): void
    {
        $this->repository
            ->method('findById')
            ->willReturn($this->createAddress());

        $this->repository
            ->method('update')
            ->willReturn($this->createAddress());

        $result = $this->service->update([
            'id' => 1,
            'user_id' => 10,
            'city' => 'Barcelona'
        ]);

        $this->assertInstanceOf(ShippingAddress::class, $result);
    }

    public function testDeleteThrowsWhenNotFound(): void
    {
        $this->repository
            ->method('findById')
            ->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->delete(1);
    }

    public function testDeleteSuccess(): void
    {
        $this->repository
            ->method('findById')
            ->willReturn($this->createAddress());

        $this->repository
            ->method('delete')
            ->willReturn(true);

        $this->service->delete(1);

        $this->assertTrue(true);
    }


    public function testDeleteByUserThrowsWhenEmpty(): void
    {
        $this->repository
            ->method('findByUserId')
            ->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->deleteByUser(10);
    }

    public function testDeleteByUserSuccess(): void
    {
        $this->repository
            ->method('findByUserId')
            ->willReturn($this->createAddress());

        $this->repository
            ->method('deleteByUserId')
            ->willReturn(true);

        $this->service->deleteByUser(10);

        $this->assertTrue(true);
    }
}
