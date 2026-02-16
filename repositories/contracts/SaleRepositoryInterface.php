<?php

namespace App\Repositories\Contracts;

use App\Models\Sale;

interface SaleRepositoryInterface
{
    public function findAll(): array;

    public function findAllDetailed(): array;

    public function findById(int $id): ?Sale;

    public function findDetailedById(int $id): ?Sale;

    public function findByUserId(int $id): array;

    public function findByProductId(int $id): array;

    public function findByStatus($status): array;

    public function findByPaymentMethod($payment_method): array;

    public function insert(Sale $sale): Sale;

    public function update(Sale $sale): Sale;

    public function delete(int $id): bool;
}
