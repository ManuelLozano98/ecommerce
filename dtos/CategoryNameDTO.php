<?php

namespace App\Dtos;

use App\Models\Category;

class CategoryNameDTO
{
    public function __construct(private Category $category) {}

    public function toArray(): array
    {
        return [
            'id' => $this->category->getId(),
            'name' => $this->category->getName(),
        ];
    }
    public static function from(array $categories): array
    {
        return array_map(
            fn(Category $c) => (new self($c))->toArray(),
            $categories
        );
    }
}
