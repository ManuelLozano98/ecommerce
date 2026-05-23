<?php

namespace App\Models;

use JsonSerializable;

class PasswordReset implements JsonSerializable
{

    private int $id;
    private int $user_id;
    private string $token;
    private string $expires_at;
    private bool $used;
    private string $created_at;

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }


    function __construct($data = [])
    {
        $this->id = $data['id'] ?? 0;
        $this->user_id = $data['user_id'] ?? 1;
        $this->token = $data['token'] ?? "";
        $this->expires_at = $data['expires_at'] ?? "";
        $this->used = $data['used'] ?? false;
        $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'token' => $this->token,
            'expires_at' => $this->expires_at,
            'used' => $this->used,
            'created_at' => $this->created_at,
        ];
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

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    public function getExpiredAt()
    {
        return $this->expires_at;
    }


    public function setExpiredAt($expires_at)
    {
        $this->expires_at = $expires_at;

        return $this;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }


    public function getCreatedAt()
    {
        return $this->created_at;
    }


    public function setUsed($used)
    {
        $this->used = $used;

        return $this;
    }


    public function isUsed()
    {
        return $this->used;
    }
}
