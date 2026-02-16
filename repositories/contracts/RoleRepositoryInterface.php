<?php

namespace App\Repositories\Contracts;

use App\Models\Role;
use App\Models\User;

interface RoleRepositoryInterface
{
    public function findAll(): array;

    public function findActive(): array;

    public function findById(int $id): ?Role;

    public function findByName(string $name): ?Role;

    public function findByActive(int $active): array;

    public function findRolesByUser(User $user): array;

    public function paginate(array $params): array;

    public function insert(Role $role): Role;

    public function update(Role $role): Role;

    public function delete(int $id): bool;
}
