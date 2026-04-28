<?php

namespace App\Repositories;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Utils\DatabaseHelper;
use App\Repositories\Contracts\SaleItemRepositoryInterface;

class SaleItemRepository implements SaleItemRepositoryInterface
{

    public function findAll(): array
    {
        $rows = DatabaseHelper::query("SELECT * FROM sale_items");
        $sale_items = [];
        foreach ($rows as $saleItem) {
            $sale_items[] = new SaleItem($saleItem);
        }
        return $sale_items;
    }

    public function findById(int $id): ?SaleItem
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM sale_items WHERE id = ?", "i", $id);
        return !empty($data) ? new SaleItem($data[0]) : null;
    }

    public function findBySale(Sale $sale): array
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM sale_items WHERE sale_id = ?", "i", $sale->getId());
        $sale_items = [];
        foreach ($data as $saleItem) {
            $sale_items[] = new SaleItem($saleItem);
        }
        return $sale_items;
    }

    public function insert(SaleItem $saleItem): SaleItem
    {
        DatabaseHelper::preparedQuery(
            "INSERT INTO sale_items (sale_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?,?,?)",
            "iiidd",
            $saleItem->getSaleId(),
            $saleItem->getProductId(),
            $saleItem->getQuantity(),
            $saleItem->getPrice(),
            $saleItem->getSubtotal()
        );

        $saleItem->setId(DatabaseHelper::getLastId());
        return $saleItem;
    }

    public function update(SaleItem $saleItem): SaleItem
    {
        DatabaseHelper::preparedQuery(
            "UPDATE sale_items SET sale_id=?, product_id=?, quantity=?, price=?, subtotal=? WHERE id=?",
            "iiiddi",
            $saleItem->getSaleId(),
            $saleItem->getProductId(),
            $saleItem->getQuantity(),
            $saleItem->getPrice(),
            $saleItem->getSubtotal(),
            $saleItem->getId()
        );

        return $saleItem;
    }
    public function delete(int $id): bool
    {
        return DatabaseHelper::preparedQuery("DELETE FROM sale_items WHERE id = ?", "i", $id);
    }
    public function deleteByIdAndSaleId(int $id, int $saleId): bool
    {
        return DatabaseHelper::preparedQuery("DELETE FROM sale_items WHERE id = ? AND sale_id = ?", "ii", $id, $saleId);
    }
}
