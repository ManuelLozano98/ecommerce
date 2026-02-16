<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use App\Models\Role;

interface UserRepositoryInterface
{
    public function findAll(): array;

    public function findById(int $id): ?User;

    public function findByUsername(string $username): ?User;

    public function findByEmail(string $email): ?User;

    public function findByPhone(string $phone): ?User;

    public function findByToken(string $token): ?User;

    public function findByName(string $name): array;

    public function findUsersByRolename(string $role_name): array;

    public function findUsersByRole(Role $role): array;

    public function countAll(): int;

    public function countUsersRegisteredLast7Days(): array;

    public function activateAccount(User $user): bool;

    public function hasRole(User $user, string $role_name): bool;

    public function paginate(array $params): array;

    public function paginateDetailed(array $params): array;

    public function insert(User $user): User;

    public function update(User $user): User;

    public function delete(int $id): bool;
}
