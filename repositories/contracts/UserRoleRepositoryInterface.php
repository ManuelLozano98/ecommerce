<?php

namespace App\Repositories\Contracts;

use App\Models\UserRole;

interface UserRoleRepositoryInterface
{
    public function findAll(): array;

    public function findById(int $id): ?UserRole;

    public function findByUserId(int $id): array;

    public function findByRoleId(int $id): array;

    public function findByUserIdAndRoleId(int $user_id, int $role_id): ?UserRole;

    public function insert(UserRole $userRole): UserRole;

    public function update(UserRole $userRole): UserRole;

    public function delete(int $id): bool;
}
