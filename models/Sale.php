<?php

namespace App\Models;

use App\Utils\DatabaseHelper;
use JsonSerializable;

class Sale implements JsonSerializable
{
    private int $id;
    private int $user_id;
    private float $total_amount;
    private string $payment_method;
    private string $status;
    private string $created_at;
    private string $updated_at;

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }


    function __construct($data = [])
    {
        $this->id = $data['id'] ?? 0;
        $this->user_id = $data['user_id'] ?? 0;
        $this->total_amount = $data['total_amount'] ?? 0.00;
        $this->payment_method = $data['payment_method'] ?? '';
        $this->status = $data['status'] ?? 'pending';
        $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
        $this->updated_at = $data['updated_at'] ?? '';
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'total_amount' => $this->total_amount,
            'updated_at' => $this->updated_at
        ];
    }

    public static function insert(Sale $sale)
    {
        $sql = "INSERT INTO sales (user_id, total_amount, payment_method, status, created_at, updated_at) VALUES (?, ?, ?,?, ?, ?)";
        $success = DatabaseHelper::preparedQuery(
            $sql,
            "idssss",
            $sale->getUserId(),
            $sale->getTotalAmount(),
            $sale->getPaymentMethod(),
            $sale->getStatus(),
            $sale->getCreatedAt(),
            $sale->getUpdatedAt()
        );

        if ($success) {
            $sale->setId(DatabaseHelper::getLastId());
            return $sale;
        }

        return false;
    }

    public static function edit(Sale $sale)
    {

        $sql = "UPDATE sales SET user_id=?, total_amount=?, payment_method=?, status=?, created_at=?, updated_at=? WHERE id=?";
        $success = DatabaseHelper::preparedQuery(
            $sql,
            "idssssi",
            $sale->getUserId(),
            $sale->getTotalAmount(),
            $sale->getPaymentMethod(),
            $sale->getStatus(),
            $sale->getCreatedAt(),
            $sale->getUpdatedAt(),
            $sale->getId()
        );
        return $success ? $sale : false;
    }
    public static function delete($id)
    {
        $sql = "DELETE FROM sales WHERE id = ?";
        return DatabaseHelper::preparedQuery($sql, "i", $id);
    }

    public static function getAll()
    {
        $sql = "SELECT * FROM sales";
        $query = DatabaseHelper::query($sql);
        $sales = [];
        foreach ($query as $sale) {
            $sales[] = new Sale($sale);
        }
        return $sales;
    }

    public static function findById($id)
    {
        $sql = "SELECT * FROM sales WHERE id=?";
        $data = DatabaseHelper::getDataPreparedQuery($sql, "i", $id);
        return !empty($data) ? new Sale($data[0]) : false;
    }
    public static function findByUserId($userId)
    {
        $sql = "SELECT * FROM sales WHERE user_id = ?";
        $query = DatabaseHelper::getDataPreparedQuery($sql, "i", $userId);

        $sales = [];
        foreach ($query as $row) {
            $sales[] = new Sale($row);
        }

        return $sales;
    }
    public static function getSalesByProductId($id)
    {
        $sql = "SELECT * FROM sales WHERE id = (SELECT sale_id FROM sale_items WHERE product_id = ?);";
        $query = DatabaseHelper::getDataPreparedQuery($sql, "i", $id);
        $sales = [];
        foreach ($query as $row) {
            $sales[] = new Sale($row);
        }

        return $sales;
    }

    public function getItems()
    {
        if (!$this->id) {
            return [];
        }

        $sql = "SELECT * FROM sale_items WHERE sale_id = ?";
        $data = DatabaseHelper::getDataPreparedQuery($sql, "i", $this->id);

        $items = [];
        foreach ($data as $row) {
            $items[] = new SaleItem($row);
        }

        return $items;
    }

    public function getId()
    {
        return $this->id;
    }
    public function getUserId()
    {
        return $this->user_id;
    }
    public function getTotalAmount()
    {
        return $this->total_amount;
    }
    public function getPaymentMethod()
    {
        return $this->payment_method;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function getCreatedAt()
    {
        return $this->created_at;
    }
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }
    public function setTotalAmount($amount)
    {
        $this->total_amount = $amount;
    }
    public function setPaymentMethod($method)
    {
        $this->payment_method = $method;
    }
    public function setStatus($status)
    {
        $this->status = $status;
    }
    public function setCreatedAt($datetime)
    {
        $this->created_at = $datetime;
    }
    public function setUpdatedAt($datetime)
    {
        $this->updated_at = $datetime;
    }
}
