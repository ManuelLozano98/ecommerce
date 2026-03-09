<?php

namespace App\Dtos;

use App\Models\Product;
use App\Models\ProductInformation;

class ProductViewDTO
{
    public Product $product;
    public ?ProductInformation $details;
    public array $images;

    public function __construct($product, $details, $images)
    {
        $this->product = $product;
        $this->details = $details;
        $this->images = $images;
    }
}
