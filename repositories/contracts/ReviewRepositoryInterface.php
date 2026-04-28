<?php

namespace App\Repositories\Contracts;

use App\Models\Review;

interface ReviewRepositoryInterface
{

    public function findAll(): array;

    public function findActive(): array;

    public function findById(int $id): ?Review;

    public function findByProductId(int $id): array;

    public function findByActive(int $active): array;

    public function findByUserId(int $id): array;

    public function findByProductIdAndUserId(int $productId, int $userId): ?Review;

    public function paginate(array $params): array;

    public function paginateDetailed(array $params): array;

    public function insert(Review $review): Review;

    public function update(Review $review): Review;

    public function delete(int $id): bool;
}
