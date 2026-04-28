<?php

namespace App\Repositories\Contracts;

use App\Models\ProductImage;

interface ProductImageRepositoryInterface
{
    public function findAll(): array;

    public function findActive(): array;

    public function findById(int $id): ?ProductImage;

    public function findByProductId(int $productId): array;

    public function findByType(string $type): array;

    public function findByActive(int $active): array;

    public function paginate(array $params): array;

    public function paginateDetailed(array $params): array;

    public function insert(ProductImage $productImage): ProductImage;

    public function update(ProductImage $productImage): ProductImage;

    public function delete(int $id): bool;
}
