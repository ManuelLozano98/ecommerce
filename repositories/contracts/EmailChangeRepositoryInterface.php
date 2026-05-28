<?php

namespace App\Repositories\Contracts;

use App\Models\EmailChange;

interface EmailChangeRepositoryInterface
{
    public function findAll(): array;

    public function findById(int $id): ?EmailChange;

    public function findByToken(string $token): ?EmailChange;

    public function findByActive(int $active): array;

    public function insert(EmailChange $email): EmailChange;

    public function update(EmailChange $email): EmailChange;

    public function delete(int $id): bool;
}
