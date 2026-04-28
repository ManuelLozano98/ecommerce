<?php

namespace App\Dtos;

use App\Models\Role;

class RoleNameDTO
{
    public function __construct(private Role $role) {}

    public function toArray(): array
    {
        return [
            'id' => $this->role->getId(),
            'name' => $this->role->getName(),
        ];
    }
    public static function from(array $roles): array
    {
        return array_map(
            fn(Role $r) => (new self($r))->toArray(),
            $roles
        );
    }
}
