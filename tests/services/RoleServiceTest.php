<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\RoleService;
use App\Models\Role;
use App\Exceptions\NotFoundException;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\DuplicateException;
use App\Repositories\Contracts\RoleRepositoryInterface;

class RoleServiceTest extends TestCase
{
    private $repository;
    private RoleService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(RoleRepositoryInterface::class);

        $this->service = new RoleService($this->repository);
    }

    private function mockRole(int $id = 1): Role
    {
        $role = $this->createMock(Role::class);
        $role->method('getId')->willReturn($id);

        return $role;
    }

    public function testGetRoleThrowsNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->getRole(1);
    }

    public function testGetRoleSuccess(): void
    {
        $role = $this->mockRole();

        $this->repository->method('findById')->willReturn($role);

        $result = $this->service->getRole(1);

        $this->assertSame($role, $result);
    }

    public function testSaveThrowsDuplicate(): void
    {
        $this->repository->method('findByName')->willReturn($this->mockRole());

        $this->expectException(DuplicateException::class);

        $this->service->save([
            'name' => 'admin'
        ]);
    }

    public function testSaveSuccess(): void
    {
        $this->repository->method('findByName')->willReturn(null);

        $role = $this->mockRole();

        $this->repository
            ->method('insert')
            ->willReturn($role);

        $result = $this->service->save([
            'name' => 'admin'
        ]);

        $this->assertSame($role, $result);
    }


    public function testSaveThrowsInsertException(): void
    {
        $this->repository->method('findByName')->willReturn(null);

        $this->repository
            ->method('insert')
            ->willThrowException(new \Exception());

        $this->expectException(InsertException::class);

        $this->service->save([
            'name' => 'admin'
        ]);
    }

    public function testUpdateThrowsNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->update([
            'id' => 1,
            'name' => 'admin'
        ]);
    }


    public function testUpdateThrowsDuplicate(): void
    {
        $role = $this->mockRole(1);
        $other = $this->mockRole(2);

        $this->repository->method('findById')->willReturn($role);
        $this->repository->method('findByName')->willReturn($other);

        $this->expectException(DuplicateException::class);

        $this->service->update([
            'id' => 1,
            'name' => 'admin'
        ]);
    }


    public function testUpdateSuccess(): void
    {
        $role = $this->mockRole(1);

        $this->repository->method('findById')->willReturn($role);
        $this->repository->method('findByName')->willReturn(null);

        $this->repository
            ->method('update')
            ->willReturn($role);

        $result = $this->service->update([
            'id' => 1,
            'name' => 'admin'
        ]);

        $this->assertSame($role, $result);
    }


    public function testUpdateThrowsUpdateException(): void
    {
        $role = $this->mockRole(1);

        $this->repository->method('findById')->willReturn($role);
        $this->repository->method('findByName')->willReturn(null);

        $this->repository
            ->method('update')
            ->willThrowException(new \Exception());

        $this->expectException(UpdateException::class);

        $this->service->update([
            'id' => 1,
            'name' => 'admin'
        ]);
    }

    public function testDeleteSuccess(): void
    {
        $role = $this->mockRole();

        $this->repository->method('findById')->willReturn($role);
        $this->repository->method('delete')->willReturn(true);

        $this->service->delete(1);

        $this->assertTrue(true);
    }


    public function testDeleteThrowsException(): void
    {
        $role = $this->mockRole();

        $this->repository->method('findById')->willReturn($role);
        $this->repository->method('delete')->willReturn(false);

        $this->expectException(DeleteException::class);

        $this->service->delete(1);
    }
}
