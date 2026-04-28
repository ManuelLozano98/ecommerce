<?php

namespace App\Dtos;


class SaleDetailedUsernameDTO
{
    public function __construct(
        public array $sales,
        public string $username,
        public array $items
    ) {
        $this->sales["items"] = $this->items;
    }

    public function toArray(): array
    {
        return [
            "sales" => $this->sales,
            "username" => $this->username
        ];
    }
}
