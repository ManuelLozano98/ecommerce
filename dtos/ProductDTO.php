<?php

namespace App\Dtos;

use App\Models\Product;

class ProductDTO
{
    public Product $product;
    public float $real_price;

    public function __construct(Product $product, float $real_price)
    {
        $this->product = $product;
        $this->real_price = $real_price;
    }
}
