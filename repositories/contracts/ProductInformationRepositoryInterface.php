<?php

namespace App\Repositories\Contracts;

use App\Models\ProductInformation;

interface ProductInformationRepositoryInterface
{
    public function findAll(): array;

    public function findById(int $id): ?ProductInformation;

    public function findByProductId(int $productId): ?ProductInformation;

    public function findFeatured(): array;

    public function findByActiveDiscount(): array;

    public function findByTechnicalDetails(array $details): array;

    public function paginate(array $params): array;

    public function paginateDetailed(array $params): array;

    public function insert(ProductInformation $productInformation): ProductInformation;

    public function update(ProductInformation $productInformation): ProductInformation;

    public function delete(int $id): bool;
}
