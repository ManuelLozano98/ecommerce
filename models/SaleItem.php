<?php

namespace App\Models;

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
