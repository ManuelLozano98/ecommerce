<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Utils\DatabaseHelper;
use App\Utils\PaginationHelper;
use App\Repositories\Contracts\CartRepositoryInterface;

class CartRepository implements CartRepositoryInterface
{

    public function findAll(): array
    {
        $rows = DatabaseHelper::query("SELECT * FROM cart_items");
        $carts = [];
        foreach ($rows as $cart) {
            $carts[] = new Cart($cart);
        }
        return $carts;
    }

    public function findById(int $id): ?Cart
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM cart_items WHERE id = ?", "i", $id);
        return !empty($data) ? new Cart($data[0]) : null;
    }

    public function findByUser(int $id): array
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM cart_items WHERE user_id = ?", "i", $id);
        $carts = [];
        foreach ($data as $cart) {
            $carts[] = new Cart($cart);
        }
        return $carts;
    }
    public function paginate(array $params): array
    {
        $tableName = "cart_items";
        $search = $params['search']['value'] ?? '';
        $columns = ['id', 'user_id', 'product_id', 'quantity'];
        $data = PaginationHelper::make($params, $tableName, $columns);


        $filteredRecords = PaginationHelper::getFilteredCount($search, $tableName, $columns);
        $totalRecords = PaginationHelper::getTotalRecords($tableName);

        $payload = [
            'data' => $data,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
        ];
        return $payload;
    }

    public function insert(Cart $cart): Cart
    {
        DatabaseHelper::preparedQuery(
            "INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)",
            "iii",
            $cart->getUserId(),
            $cart->getProduct()->getId(),
            $cart->getQuantity()
        );

        $cart->setId(DatabaseHelper::getLastId());
        return $cart;
    }

    public function update(Cart $cart): Cart
    {
        DatabaseHelper::preparedQuery(
            "UPDATE cart_items SET user_id=?, product_id=?, quantity=? WHERE id=?",
            "iiii",
            $cart->getUserId(),
            $cart->getProduct()->getId(),
            $cart->getQuantity(),
            $cart->getId()
        );

        return $cart;
    }
    public function delete(int $id): bool
    {
        return DatabaseHelper::preparedQuery("DELETE FROM cart_items WHERE id = ?", "i", $id);
    }
}
