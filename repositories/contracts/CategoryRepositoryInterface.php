<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use App\Models\Product;

interface CategoryRepositoryInterface
{
    public function findAll(): array;

    public function findActive(): array;

    public function findById(int $id): ?Category;

    public function findByName(string $name): ?Category;

    public function findByActive(int $active): array;

    public function findBySlug(string $slug): ?Category;

    public function findByProduct(Product $product): ?Category;

    public function paginate(array $params): array;

    public function insert(Category $category): Category;

    public function update(Category $category): Category;

    public function delete(int $id): bool;
}
