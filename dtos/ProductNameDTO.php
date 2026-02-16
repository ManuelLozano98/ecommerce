<?php

namespace App\Dtos;

use App\Models\Product;

class ProductNameDTO
{
    public function __construct(private Product $product) {}

    public function toArray(): array
    {
        return [
            'id' => $this->product->getId(),
            'name' => $this->product->getName(),
        ];
    }
    public static function from(array $products): array
    {
        return array_map(
            fn(Product $p) => (new self($p))->toArray(),
            $products
        );
    }
}
