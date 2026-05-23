<?php

namespace App\Repositories\Contracts;

use App\Models\Cart;

interface CartRepositoryInterface
{
    public function findAll(): array;

    public function findById(int $id): ?Cart;

    public function findByUser(int $id): array;

    public function paginate(array $params): array;

    public function insert(Cart $cart): Cart;

    public function update(Cart $cart): Cart;

    public function delete(int $id): bool;

    public function deleteUserCart(int $userId): bool;

    public function deleteByUserAndProduct(int $userId, int $id): bool;
}
