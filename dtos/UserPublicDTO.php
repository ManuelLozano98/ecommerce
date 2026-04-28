<?php

namespace App\Dtos;

use App\Models\User;

class UserPublicDTO
{
    private int $id;
    private string $username;
    private string $image;

    public function __construct(User $user)
    {
        $this->id = $user->getId();
        $this->username = $user->getUsername();
        $this->image = $user->getImage();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getImage(): string
    {
        return $this->image;
    }
}
