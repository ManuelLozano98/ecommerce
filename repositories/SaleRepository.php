<?php

namespace App\Repositories;

use App\Models\Sale;
use App\Repositories\Contracts\SaleItemRepositoryInterface;
use App\Utils\DatabaseHelper;
use App\Repositories\Contracts\SaleRepositoryInterface;

class SaleRepository implements SaleRepositoryInterface
{
    private SaleItemRepositoryInterface $sale_item;

    public function __construct(SaleItemRepositoryInterface $sale_item)
    {
        $this->sale_item = $sale_item;
    }

    public function findAll(): array
    {
        $rows = DatabaseHelper::query("SELECT * FROM sales");
        $sales = [];
        foreach ($rows as $sale) {
            $sales[] = new Sale($sale);
        }
        return $sales;
    }

    public function findAllDetailed(): array
    {
        $sales = $this->findAll();
        if (!$sales) {
            return [];
        }
        foreach ($sales as $sale) {
            $sale->setItems($this->sale_item->findBySale($sale));
        }

        return $sales;
    }


    public function findById(int $id): ?Sale
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM sales WHERE id = ?", "i", $id);
        return !empty($data) ? new Sale($data[0]) : null;
    }

    public function findDetailedById(int $id): ?Sale
    {
        $saleData = $this->findById($id);

        if (!$saleData) {
            return null;
        }

        $items = $this->sale_item->findBySale($saleData);

        $saleData->setItems($items);

        return $saleData;
    }

    public function findByStatus($status): array
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM sales WHERE status = ?", "s", $status);
        $sales = [];
        foreach ($data as $sale) {
            $sales[] = new Sale($sale);
        }
        return $sales;
    }

    public function findByPaymentMethod($payment_method): array
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM sales WHERE payment_method = ?", "s", $payment_method);
        $sales = [];
        foreach ($data as $sale) {
            $sales[] = new Sale($sale);
        }
        return $sales;
    }

    public function findByUserId(int $id): array
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM sales WHERE user_id = ?", "i", $id);
        $sales = [];
        foreach ($data as $row) {
            $sales[] = new Sale($row);
        }

        return $sales;
    }

    public function findByProductId(int $id): array
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM sales WHERE id IN (SELECT sale_id FROM sale_items WHERE product_id = ?);", "i", $id);
        $sales = [];
        foreach ($data as $row) {
            $sales[] = new Sale($row);
        }

        return $sales;
    }

    public function insert(Sale $sale): Sale
    {
        DatabaseHelper::preparedQuery(
            "INSERT INTO sales (user_id, total_amount, payment_method, status, created_at, updated_at) VALUES (?, ?, ?,?, ?, ?)",
            "idssss",
            $sale->getUserId(),
            $sale->getTotalAmount(),
            $sale->getPaymentMethod()->value,
            $sale->getStatus()->value,
            $sale->getCreatedAt(),
            $sale->getUpdatedAt()
        );

        $sale->setId(DatabaseHelper::getLastId());
        return $sale;
    }

    public function update(Sale $sale): Sale
    {
        DatabaseHelper::preparedQuery(
            "UPDATE sales SET user_id=?, total_amount=?, payment_method=?, status=?, created_at=?, updated_at=? WHERE id=?",
            "idssssi",
            $sale->getUserId(),
            $sale->getTotalAmount(),
            $sale->getPaymentMethod()->value,
            $sale->getStatus()->value,
            $sale->getCreatedAt(),
            $sale->getUpdatedAt(),
            $sale->getId()
        );

        return $sale;
    }
    public function delete(int $id): bool
    {
        return DatabaseHelper::preparedQuery("DELETE FROM sales WHERE id = ?", "i", $id);
    }
}
