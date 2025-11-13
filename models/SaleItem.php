<?php

namespace App\Models;

use App\Utils\DatabaseHelper;
use JsonSerializable;

class SaleItem implements JsonSerializable
{
    private int $id;
    private int $sale_id;
    private int $product_id;
    private int $quantity;
    private float $price;
    private float $subtotal;

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }


    function __construct($data = [])
    {
        $this->id = $data['id'] ?? 0;
        $this->sale_id = $data['sale_id'] ?? 0;
        $this->product_id = $data['product_id'] ?? 0;
        $this->quantity = $data['quantity'] ?? 0;
        $this->price = $data['price'] ?? 0.0;
        $this->subtotal = $data['subtotal'] ?? 0.0;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'sale_id' => $this->sale_id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'subtotal' => $this->subtotal
        ];
    }

    public static function insert(SaleItem $saleItem)
    {
        $sql = "INSERT INTO sale_items (sale_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?,?,?)";
        $success = DatabaseHelper::preparedQuery(
            $sql,
            "iiidd",
            $saleItem->getSaleId(),
            $saleItem->getProductId(),
            $saleItem->getQuantity(),
            $saleItem->getPrice(),
            $saleItem->getSubtotal()
        );

        if ($success) {
            $saleItem->setId(DatabaseHelper::getLastId());
            return $saleItem;
        }

        return false;
    }

    public static function edit(SaleItem $saleItem)
    {

        $sql = "UPDATE sale_items SET sale_id=?, product_id=?, quantity=?, price=?, subtotal=? WHERE id=?";
        $success = DatabaseHelper::preparedQuery(
            $sql,
            "iiiddi",
            $saleItem->getSaleId(),
            $saleItem->getProductId(),
            $saleItem->getQuantity(),
            $saleItem->getPrice(),
            $saleItem->getSubtotal(),
            $saleItem->getId()
        );
        return $success ? $saleItem : false;
    }
    public static function delete($id)
    {
        $sql = "DELETE FROM sale_items WHERE id = ?";
        return DatabaseHelper::preparedQuery($sql, "i", $id);
    }

    public static function deleteBySaleId($idSale, $id)
    {
        $sql = "DELETE FROM sale_items WHERE id=? AND sale_id=?";
        return DatabaseHelper::preparedQuery($sql, "ii", $id, $idSale);
    }
    public static function getAll()
    {
        $sql = "SELECT * FROM sale_items";
        $query = DatabaseHelper::query($sql);
        $sale_items = [];
        foreach ($query as $saleItem) {
            $sale_items[] = new SaleItem($saleItem);
        }
        return $sale_items;
    }

    public static function findById($id)
    {
        $sql = "SELECT * FROM sale_items WHERE id=?";
        $data = DatabaseHelper::getDataPreparedQuery($sql, "i", $id);
        return !empty($data) ? new SaleItem($data[0]) : false;
    }



    public function getId()
    {
        return $this->id;
    }
    public function getSaleId()
    {
        return $this->sale_id;
    }
    public function getProductId()
    {
        return $this->product_id;
    }
    public function getQuantity()
    {
        return $this->quantity;
    }
    public function getPrice()
    {
        return $this->price;
    }
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    public function setSaleId($sale_id)
    {
        $this->sale_id = $sale_id;
    }
    public function setProductId($product_id)
    {
        $this->product_id = $product_id;
    }
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }
    public function setPrice($price)
    {
        $this->price = $price;
    }
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;
    }
}
