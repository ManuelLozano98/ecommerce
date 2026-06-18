<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\UserRoleService;
use App\Models\UserRole;
use App\Models\User;
use App\Models\Role;
use App\Exceptions\NotFoundException;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\DuplicateException;
use App\Repositories\Contracts\UserRoleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;

class UserRoleServiceTest extends TestCase
{
    private $userRoleRepository;
    private $userRepository;
    private $roleRepository;
    private UserRoleService $service;

    protected function setUp(): void
    {
        $this->userRoleRepository = $this->createMock(UserRoleRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->roleRepository = $this->createMock(RoleRepositoryInterface::class);

        $this->service = new UserRoleService(
            $this->userRoleRepository,
            $this->userRepository,
            $this->roleRepository
        );
    }

    private function mockUser()
    {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(1);
        return $user;
    }

    private function mockRole()
    {
        $role = $this->createMock(Role::class);
        $role->method('getId')->willReturn(1);
        return $role;
    }

    private function mockUserRole(int $id = 1)
    {
        $ur = $this->createMock(UserRole::class);
        $ur->method('getId')->willReturn($id);
        $ur->method('getUserId')->willReturn(1);
        $ur->method('getRoleId')->willReturn(1);

        return $ur;
    }

    public function testGetUserRoleByIdNotFound(): void
    {
        $this->userRoleRepository->method('findById')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->getUserRoleById(1);
    }

    public function testGetUserRoleByIdSuccess(): void
    {
        $ur = $this->mockUserRole();

        $this->userRoleRepository->method('findById')->willReturn($ur);

        $result = $this->service->getUserRoleById(1);

        $this->assertSame($ur, $result);
    }

    public function testSaveThrowsUserNotFound(): void
    {
        $this->userRepository->method('findById')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->save([
            'user_id' => 1,
            'role_id' => 1
        ]);
    }

    public function testSaveThrowsRoleNotFound(): void
    {
        $this->userRepository->method('findById')->willReturn($this->mockUser());
        $this->roleRepository->method('findById')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->save([
            'user_id' => 1,
            'role_id' => 1
        ]);
    }


    public function testSaveThrowsDuplicate(): void
    {
        $this->userRepository->method('findById')->willReturn($this->mockUser());
        $this->roleRepository->method('findById')->willReturn($this->mockRole());
        $this->userRoleRepository->method('findByUserIdAndRoleId')->willReturn($this->mockUserRole());

        $this->expectException(DuplicateException::class);

        $this->service->save([
            'user_id' => 1,
            'role_id' => 1
        ]);
    }

    public function testSaveSuccess(): void
    {
        $this->userRepository->method('findById')->willReturn($this->mockUser());
        $this->roleRepository->method('findById')->willReturn($this->mockRole());
        $this->userRoleRepository->method('findByUserIdAndRoleId')->willReturn(null);

        $ur = $this->mockUserRole();

        $this->userRoleRepository
            ->method('insert')
            ->willReturn($ur);

        $result = $this->service->save([
            'user_id' => 1,
            'role_id' => 1
        ]);

        $this->assertSame($ur, $result);
    }


    public function testSaveThrowsInsertException(): void
    {
        $this->userRepository->method('findById')->willReturn($this->mockUser());
        $this->roleRepository->method('findById')->willReturn($this->mockRole());
        $this->userRoleRepository->method('findByUserIdAndRoleId')->willReturn(null);

        $this->userRoleRepository
            ->method('insert')
            ->willThrowException(new \Exception());

        $this->expectException(InsertException::class);

        $this->service->save([
            'user_id' => 1,
            'role_id' => 1
        ]);
    }


    public function testUpdateThrowsNotFound(): void
    {
        $this->userRoleRepository->method('findById')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->update([
            'id' => 1,
            'user_id' => 1,
            'role_id' => 1
        ]);
    }

    public function testUpdateThrowsDuplicate(): void
    {
        $existing = $this->mockUserRole(1);
        $other = $this->mockUserRole(2);

        $this->userRoleRepository->method('findById')->willReturn($existing);
        $this->userRoleRepository->method('findByUserIdAndRoleId')->willReturn($other);

        $this->expectException(DuplicateException::class);

        $this->service->update([
            'id' => 1,
            'user_id' => 1,
            'role_id' => 1
        ]);
    }


    public function testUpdateSuccess(): void
    {
        $ur = $this->mockUserRole();

        $this->userRoleRepository->method('findById')->willReturn($ur);
        $this->userRoleRepository->method('findByUserIdAndRoleId')->willReturn(null);

        $this->userRoleRepository
            ->method('update')
            ->willReturn($ur);

        $result = $this->service->update([
            'id' => 1,
            'user_id' => 1,
            'role_id' => 1
        ]);

        $this->assertSame($ur, $result);
    }


    public function testUpdateThrowsUpdateException(): void
    {
        $ur = $this->mockUserRole();

        $this->userRoleRepository->method('findById')->willReturn($ur);
        $this->userRoleRepository->method('findByUserIdAndRoleId')->willReturn(null);

        $this->userRoleRepository
            ->method('update')
            ->willThrowException(new \Exception());

        $this->expectException(UpdateException::class);

        $this->service->update([
            'id' => 1,
            'user_id' => 1,
            'role_id' => 1
        ]);
    }


    public function testDeleteSuccess(): void
    {
        $ur = $this->mockUserRole();

        $this->userRoleRepository->method('findById')->willReturn($ur);
        $this->userRoleRepository->method('delete')->willReturn(true);

        $this->service->delete(1);

        $this->assertTrue(true);
    }


    public function testDeleteThrowsException(): void
    {
        $ur = $this->mockUserRole();

        $this->userRoleRepository->method('findById')->willReturn($ur);
        $this->userRoleRepository->method('delete')->willReturn(false);

        $this->expectException(DeleteException::class);

        $this->service->delete(1);
    }
}
