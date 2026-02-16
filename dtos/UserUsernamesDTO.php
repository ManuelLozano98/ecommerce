<?php

namespace App\Dtos;

use App\Models\User;

class UserUsernamesDTO
{
    public function __construct(private User $user) {}

    public function toArray(): array
    {
        return [
            'id' => $this->user->getId(),
            'username' => $this->user->getUsername(),
        ];
    }
    public static function from(array $users): array
    {
        return array_map(
            fn(User $u) => (new self($u))->toArray(),
            $users
        );
    }
}
