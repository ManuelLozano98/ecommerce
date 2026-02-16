<?php

namespace App\Dtos;


class UserRoleDetailedDTO
{
    public function __construct(
        public array $users,
        public array $roles
    ) {
        $this->users["roles"] = $roles;
    }

    public function toArray(): array
    {
        return [
            "users" => $this->users,
        ];
    }
}
