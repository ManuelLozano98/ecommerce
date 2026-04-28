<?php

namespace App\Models;

use JsonSerializable;

class Review implements JsonSerializable
{
    private int $id;
    private int $product_id;
    private int $user_id;
    private string $title;
    private string $comment;
    private float $rating;
    private string $created_at;
    private string $updated_at;
    private bool $active;


    function __construct($data = [])
    {
        $this->id = $data['id'] ?? 0;
        $this->product_id = $data['product_id'] ?? 0;
        $this->user_id = $data['user_id'] ?? 0;
        $this->title = $data['title'] ?? '';
        $this->comment = $data['comment'] ?? '';
        $this->rating = $data['rating'] ?? 0.0;
        $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
        $this->updated_at = $data['updated_at'] ?? date('Y-m-d H:i:s');
        $this->active = (bool) ($data['active'] ?? true);
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'comment' => $this->comment,
            'rating' => $this->rating,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'active' => $this->active
        ];
    }

    public function getId()
    {
        return $this->id;
    }
    public function getProductId()
    {
        return $this->product_id;
    }
    public function getUserId()
    {
        return $this->user_id;
    }
    public function getTitle()
    {
        return $this->title;
    }
    public function getComment()
    {
        return $this->comment;
    }
    public function getRating()
    {
        return $this->rating;
    }
    public function getCreatedAt()
    {
        return $this->created_at;
    }
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }
    public function getActive()
    {
        return $this->active;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }
    public function setProductId(int $id)
    {
        $this->product_id = $id;
    }
    public function setUserId(int $id)
    {
        $this->user_id = $id;
    }
    public function setTitle(string $title)
    {
        $this->title = $title;
    }
    public function setComment(string $comment)
    {
        $this->comment = $comment;
    }
    public function setRating(float $rating)
    {
        $this->rating = $rating;
    }
    public function setUpdatedAt(?string $date)
    {
        $this->updated_at = $date;
    }
    public function setActive(bool $active)
    {
        $this->active = $active;
    }
}
