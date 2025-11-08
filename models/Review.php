<?php

namespace App\Models;

use App\Utils\DatabaseHelper;
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

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }


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
        $this->active = $data['active'] ?? 1;
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

    public static function insert(Review $review)
    {
        $sql = "INSERT INTO reviews (product_id, user_id, title, comment, rating, created_at, updated_at, active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $success = DatabaseHelper::preparedQuery(
            $sql,
            "iissdssi",
            $review->getProductId(),
            $review->getUserId(),
            $review->getTitle(),
            $review->getComment(),
            $review->getRating(),
            $review->getCreatedAt(),
            $review->getUpdatedAt(),
            $review->getActive()
        );

        if ($success) {
            $review->setId(DatabaseHelper::getLastId());
            return $review;
        }

        return false;
    }

    public static function edit(Review $review)
    {

        $sql = "UPDATE reviews SET product_id=?, user_id=?, title=?, comment=?, rating=?, created_at=?, updated_at=?, active=? WHERE id=?";
        $success = DatabaseHelper::preparedQuery(
            $sql,
            "iissdssii",
            $review->getProductId(),
            $review->getUserId(),
            $review->getTitle(),
            $review->getComment(),
            $review->getRating(),
            $review->getCreatedAt(),
            $review->getUpdatedAt(),
            $review->getActive(),
            $review->getId()
        );
        return $success ? $review : false;
    }
    public static function delete($id)
    {
        $sql = "DELETE FROM reviews WHERE id = ?";
        return DatabaseHelper::preparedQuery($sql, "i", $id);
    }

    public static function getAll()
    {
        $sql = "SELECT * FROM reviews";
        $query = DatabaseHelper::query($sql);
        $reviews = [];
        foreach ($query as $review) {
            $reviews[] = new Review($review);
        }
        return $reviews;
    }

    public static function findById($id)
    {
        $sql = "SELECT * FROM reviews WHERE id=?";
        $data = DatabaseHelper::getDatapreparedQuery($sql, "i", $id);
        return !empty($data) ? new Review($data[0]) : false;
    }

    public static function findByProductId($id)
    {
        $sql = "SELECT * FROM reviews WHERE product_id=?";
        $query = DatabaseHelper::getDataPreparedQuery($sql, "i", $id);
        $reviews = [];
        foreach ($query as $row) {
            $reviews[] = new Review($row);
        }
        return $reviews;
    }

    public static function findByUserId($id)
    {
        $sql = "SELECT * FROM reviews WHERE user_id=?";
        $query = DatabaseHelper::getDataPreparedQuery($sql, "i", $id);
        $reviews = [];
        foreach ($query as $row) {
            $reviews[] = new Review($row);
        }
        return $reviews;
    }

    public static function findByProductIdAndUserId($productId, $userId)
    {
        $review = null;
        $sql = "SELECT * FROM reviews WHERE user_id=? AND product_id=?";
        $query = DatabaseHelper::getDataPreparedQuery($sql, "ii", $userId, $productId);
        if (count($query) > 0) {
            $review = new Review($query[0]);
        }
        return $review;
    }

    public static function hasReview($productId, $userId)
    {
        $sql = "SELECT * FROM reviews WHERE user_id=? AND product_id=?";
        $query = DatabaseHelper::getDataPreparedQuery($sql, "ii", $userId, $productId);
        return count($query) > 0;
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
