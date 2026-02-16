<?php

namespace App\Dtos;


class SaleDetailedDTO
{
    public function __construct(
        public array $sales,
        public array $items
    ) {
        $this->sales["items"] = $items;
    }

    public function toArray(): array
    {
        return [
            "sales" => $this->sales

        ];
    }
}
