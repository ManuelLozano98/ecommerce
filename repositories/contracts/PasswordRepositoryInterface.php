<?php

namespace App\Repositories\Contracts;

use App\Models\PasswordReset;

interface PasswordRepositoryInterface
{
    public function findAll(): array;

    public function findById(int $id): ?PasswordReset;

    public function findByToken(string $token): ?PasswordReset;

    public function findByActive(int $active): array;

    public function findByUser(int $id): ?PasswordReset;

    public function insert(PasswordReset $password): PasswordReset;

    public function update(PasswordReset $password): PasswordReset;

    public function delete(int $id): bool;
}
