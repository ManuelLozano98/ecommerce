<?php

namespace App\Models;

use JsonSerializable;

class UserRole implements JsonSerializable
{
    private int $id;
    private int $roleId;
    private int $userId;

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }


    function __construct($data = [])
    {
        $this->id = $data['id'] ?? 0;
        $this->roleId = $data['role_id'] ?? 0;
        $this->userId = $data['user_id'] ?? 0;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'role_id' => $this->roleId,
            'user_id' => $this->userId,
        ];
    }

    public function getRoleId()
    {
        return $this->roleId;
    }
    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;

        return $this;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }


    public function getUserId()
    {
        return $this->userId;
    }


    public function getId()
    {
        return $this->id;
    }


    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
