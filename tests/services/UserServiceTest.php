<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\UserService;
use App\Models\User;
use App\Exceptions\NotFoundException;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\DuplicateException;
use App\Exceptions\InvalidCredentialsException;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserServiceTest extends TestCase
{
    private UserRepositoryInterface $repository;
    private UserService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(UserRepositoryInterface::class);
        $this->service = new UserService($this->repository);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
    }

    private function mockUser(int $id = 1): User
    {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($id);
        $user->method('getActive')->willReturn(true);
        $user->method('getPassword')->willReturn(password_hash('123', PASSWORD_ARGON2ID));

        $user->method('getUsername')->willReturn('john');
        $user->method('getEmail')->willReturn('john@test.com');
        $user->method('getName')->willReturn('John');
        $user->method('getImage')->willReturn('default.png');

        return $user;
    }

    public function testGetUserThrowsNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->getUser(1);
    }

    public function testGetUserReturnsUser(): void
    {
        $user = $this->mockUser();

        $this->repository->method('findById')->willReturn($user);

        $result = $this->service->getUser(1);

        $this->assertSame($user, $result);
    }

    public function testDeleteThrowsNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->delete(1);
    }

    public function testDeleteSuccess(): void
    {
        $this->repository->method('findById')->willReturn($this->mockUser());
        $this->repository->method('delete')->willReturn(true);

        $this->service->delete(1);

        $this->assertTrue(true);
    }

    public function testDeleteThrowsExceptionWhenFails(): void
    {
        $this->repository->method('findById')->willReturn($this->mockUser());
        $this->repository->method('delete')->willReturn(false);

        $this->expectException(DeleteException::class);

        $this->service->delete(1);
    }

    public function testSaveThrowsDuplicate(): void
    {
        $this->repository->method('findByUsername')->willReturn($this->mockUser());
        $this->repository->method('findByEmail')->willReturn(null);

        $this->expectException(DuplicateException::class);

        $this->service->save([
            'username' => 'test',
            'email' => 'test@test.com',
            'password' => '123'
        ]);
    }

    public function testSaveReturnsUser(): void
    {
        $this->repository->method('findByUsername')->willReturn(null);
        $this->repository->method('findByEmail')->willReturn(null);

        $this->repository
            ->method('insert')
            ->willReturn($this->mockUser());

        $result = $this->service->save([
            'username' => 'test',
            'email' => 'test@test.com',
            'password' => '123'
        ]);

        $this->assertInstanceOf(User::class, $result);
    }

    public function testSaveThrowsInsertException(): void
    {
        $this->repository->method('findByUsername')->willReturn(null);
        $this->repository->method('findByEmail')->willReturn(null);

        $this->repository
            ->method('insert')
            ->willThrowException(new \Exception('DB error'));

        $this->expectException(InsertException::class);

        $this->service->save([
            'username' => 'test',
            'email' => 'test@test.com',
            'password' => '123'
        ]);
    }

    public function testUpdateSuccess(): void
    {
        $user = $this->mockUser();

        $this->repository->method('findById')->willReturn($user);
        $this->repository->method('findByUsername')->willReturn(null);
        $this->repository->method('findByEmail')->willReturn(null);
        $this->repository->method('update')->willReturn($this->mockUser());

        $result = $this->service->update([
            'id' => 1,
            'username' => 'test',
            'email' => 'test@test.com'
        ]);

        $this->assertInstanceOf(User::class, $result);
    }

    public function testUpdateThrowsNotFound(): void
    {
        $this->repository->method('findById')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->update([
            'id' => 1,
            'username' => 'test',
            'email' => 'test@test.com'
        ]);
    }

    public function testUpdateThrowsDuplicateWhenUsernameExists(): void
    {
        $user = $this->mockUser(1);
        $otherUser = $this->mockUser(2);

        $this->repository->method('findById')->willReturn($user);
        $this->repository->method('findByUsername')->willReturn($otherUser);

        $this->expectException(DuplicateException::class);

        $this->service->update([
            'id' => 1,
            'username' => 'existing',
            'email' => 'test@test.com'
        ]);
    }

    public function testUpdateThrowsDuplicateWhenEmailExists(): void
    {
        $user = $this->mockUser(1);
        $otherUser = $this->mockUser(2);

        $this->repository->method('findById')->willReturn($user);
        $this->repository->method('findByUsername')->willReturn(null);
        $this->repository->method('findByEmail')->willReturn($otherUser);

        $this->expectException(DuplicateException::class);

        $this->service->update([
            'id' => 1,
            'username' => 'test',
            'email' => 'existing@test.com'
        ]);
    }

    public function testUpdateThrowsUpdateException(): void
    {
        $user = $this->mockUser();

        $this->repository->method('findById')->willReturn($user);
        $this->repository->method('findByUsername')->willReturn(null);
        $this->repository->method('findByEmail')->willReturn(null);

        $this->repository
            ->method('update')
            ->willThrowException(new \Exception());

        $this->expectException(UpdateException::class);

        $this->service->update([
            'id' => 1,
            'username' => 'test',
            'email' => 'test@test.com'
        ]);
    }

    public function testLoginInvalidCredentials(): void
    {
        $this->repository->method('findByEmail')->willReturn(null);
        $this->repository->method('findByUsername')->willReturn(null);

        $this->expectException(InvalidCredentialsException::class);

        $this->service->logIn([
            'login' => 'test',
            'password' => '123'
        ]);
    }

    public function testLoginSuccess(): void
    {
        $user = $this->mockUser();

        $this->repository->method('findByEmail')->willReturn($user);

        $result = $this->service->logIn([
            'login' => 'test@test.com',
            'password' => '123'
        ]);

        $this->assertTrue($result);
        $this->assertArrayHasKey('user', $_SESSION);
    }

    public function testLoginInactiveUser(): void
    {
        $user = $this->createMock(User::class);

        $user->method('getActive')->willReturn(false);

        $this->repository->method('findByEmail')->willReturn($user);

        $this->expectException(\Exception::class);

        $this->service->logIn([
            'login' => 'test@test.com',
            'password' => '123'
        ]);
    }

    public function testLoginWrongPassword(): void
    {
        $user = $this->mockUser();

        $this->repository->method('findByEmail')->willReturn($user);

        $this->expectException(InvalidCredentialsException::class);

        $this->service->logIn([
            'login' => 'test@test.com',
            'password' => 'wrong'
        ]);
    }

    public function testUpdateEmailThrowsNotFound(): void
    {
        $user = $this->mockUser();

        $this->repository->method('findById')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->updateEmail($user, 'new@test.com');
    }

    public function testUpdateEmailThrowsUpdateException(): void
    {
        $user = $this->mockUser();

        $this->repository->method('findById')->willReturn($user);

        $this->repository
            ->method('updateEmail')
            ->willThrowException(new \Exception('DB error'));

        $this->expectException(UpdateException::class);

        $this->service->updateEmail($user, 'new@test.com');
    }

    public function testUpdateEmailSuccess(): void
    {
        $user = $this->mockUser();

        $this->repository->method('findById')->willReturn($user);

        $this->repository
            ->method('updateEmail')
            ->willReturn($user);

        $result = $this->service->updateEmail(
            $user,
            'new@test.com'
        );

        $this->assertSame($user, $result);
    }

    public function testUpdatePasswordThrowsUpdateException(): void
    {
        $user = $this->mockUser();

        $this->repository
            ->method('updatePassword')
            ->willThrowException(new \Exception('DB error'));

        $this->expectException(UpdateException::class);

        $this->service->updatePassword(1, '123456');
    }

    public function testUpdatePasswordSuccess(): void
    {
        $user = $this->mockUser();

        $this->repository
            ->method('updatePassword')
            ->willReturn($user);

        $result = $this->service->updatePassword(
            1,
            '123456'
        );

        $this->assertTrue($result);
    }

    public function testUpdateTokenThrowsNotFound(): void
    {
        $user = $this->mockUser();

        $this->repository->method('findById')->willReturn(null);

        $this->expectException(NotFoundException::class);

        $this->service->updateToken($user);
    }


    public function testUpdateTokenThrowsUpdateException(): void
    {
        $user = $this->mockUser();

        $this->repository->method('findById')->willReturn($user);

        $this->repository
            ->method('updateToken')
            ->willThrowException(new \Exception('DB error'));

        $this->expectException(UpdateException::class);

        $this->service->updateToken($user);
    }

    public function testUpdateTokenSuccess(): void
    {
        $user = $this->mockUser();

        $this->repository->method('findById')->willReturn($user);

        $this->repository
            ->method('updateToken')
            ->willReturn($user);

        $result = $this->service->updateToken($user);

        $this->assertSame($user, $result);
    }
}
