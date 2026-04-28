<?php

namespace App\Dtos;

use App\Models\Product;
use App\Models\Category;

class ProductWithCategoryDTO
{
    public function __construct(private Product $product, private Category $category) {}

    public function toArray(): array
    {
        return array_merge(
            $this->product->toArray(),
            [
                "category" =>
                [
                    "id" => $this->category->getId(),
                    "name" => $this->category->getName()
                ]
            ]
        );
    }

    public static function from(array $products, array $categories): array
    {
        return array_map(
            fn(Product $p, Category $c) => (new self($p, $c))->toArray(),
            $products,
            $categories
        );
    }
}
