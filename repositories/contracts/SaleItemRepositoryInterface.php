<?php

namespace App\Repositories\Contracts;

use App\Models\SaleItem;
use App\Models\Sale;

interface SaleItemRepositoryInterface
{
    public function findAll(): array;

    public function findById(int $id): ?SaleItem;

    public function findBySale(Sale $sale): array;

    public function insert(SaleItem $saleItem): SaleItem;

    public function update(SaleItem $saleItem): SaleItem;

    public function delete(int $id): bool;

    public function deleteByIdAndSaleId(int $id, int $sale_id): bool;
}
