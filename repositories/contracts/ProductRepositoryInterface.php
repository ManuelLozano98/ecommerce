<?php

namespace App\Repositories\Contracts;

use App\Models\Product;

interface ProductRepositoryInterface
{
    public function findAll(): array;

    public function findActive(): array;

    public function findById(int $id): ?Product;

    public function findByName(string $name): ?Product;

    public function findByCode(string $code): ?Product;

    public function findByActive(int $active): array;

    public function findBySlug(string $slug): ?Product;

    public function findByCategory(int $categoryId): array;

    public function findActivePaginated(int $limit, int $offset): array;

    public function findActiveByCategoryPaginated(string $category, int $limit, int $offset): array;

    public function findByPriceRange(?float $min, ?float $max, string $order = "ASC"): array;

    public function findTopSellers(): array;

    public function findByMinReviewScore(int $score): array;

    public function findOrderedByReviewScore(string $order): array;

    public function findLowestPrice(): ?Product;

    public function findHigherPrice(): ?Product;

    public function applyFilters(array $filters, int $limit, int $offset): array;

    public function countAll(): int;

    public function countNew(): int;

    public function countFiltered(array $filters): int;

    public function paginate(array $params): array;

    public function paginateDetailed(array $params): array;

    public function insert(Product $product): Product;

    public function update(Product $product): Product;

    public function delete(int $id): bool;
}
