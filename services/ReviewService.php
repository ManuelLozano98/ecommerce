<?php

namespace App\Services;

use App\Models\Review;
use App\Models\User;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\DuplicateException;
use App\Exceptions\ForeignKeyException;
use App\Exceptions\NotFoundException;

class ReviewService
{
    public function getReviews()
    {
        return Review::getAll();
    }
    public function getReview($id)
    {
        $review = Review::findById($id);
        if (!$review) {
            throw new NotFoundException("The review was not found or does not exits");
        }
        return $review;
    }
    public function getReviewsByProduct($id)
    {
        $review = Review::findByProductId($id);
        if (!$review) {
            throw new NotFoundException("The review was not found or does not exits");
        }
        return $review;
    }
    public function getReviewsByUser($id)
    {
        $review = Review::findByUserId($id);
        if (!$review) {
            throw new NotFoundException("The review was not found or does not exits");
        }
        return $review;
    }
    public function deleteReview($id)
    {
        if (!Review::findById($id)) {
            throw new NotFoundException("The review was not found or not exists");
        }

        if (!Review::delete($id)) {
            throw new DeleteException("Failed to delete review with ID $id.");
        }
    }
    public function deleteReviewsByUser(User $user)
    {
        if (!$user) {
            throw new NotFoundException("The review was not found or not exists");
        }
        $userReviews = Review::findByUserId($user->getId());
        if (!$userReviews) {
            throw new NotFoundException("The user has no reviews");
        }
        foreach ($userReviews as $review) {
            if (!Review::delete($review->getId())) {
                throw new DeleteException("Failed to delete review with ID " . $review->getId());
            }
        }
    }
    public function saveReview($method, $rawReview, UserService $userService, ProductService $productService)
    {
        if ($method === "POST") {
            if (!$userService->getUser($rawReview["user_id"])) {
                throw new NotFoundException("The review was not found or not exists");
            }
            if (!$productService->getProduct($rawReview["product_id"])) {
                throw new NotFoundException("The review was not found or not exists");
            }

            if (Review::hasReview($rawReview["product_id"], $rawReview["user_id"])) {
                throw new DuplicateException("The user has a review of the product");
            }
            $review = new Review($rawReview);
            if (!Review::insert($review)) {
                throw new InsertException("Failed to insert review");
            }
            return $review;
        } else {
            $reviewDb = Review::findById($rawReview["id"]);
            if (!$reviewDb) {
                throw new NotFoundException("The review was not found or not exists");
            }
            if (!$productService->getProduct($rawReview["product_id"])) {
                throw new NotFoundException("The review was not found or not exists");
            }
            if (!$userService->getUser($rawReview["user_id"])) {
                throw new NotFoundException("The review was not found or not exists");
            }
            $editReview = $this->set($reviewDb, $rawReview);
            if (!Review::edit($editReview)) {
                throw new UpdateException("Failed to update review with ID " . $reviewDb->getId());
            }
            return $editReview;
        }
    }
    private function set($reviewDb, $rawReview)
    {
        $allowedFields = ['active', 'title', 'comment', 'rating'];
        $reviewDb->setUpdatedAt(date('Y-m-d H:i:s'));
        foreach ($allowedFields as $field) {
            if (isset($rawReview[$field])) {
                $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));

                if (method_exists($reviewDb, $method)) {
                    $reviewDb->$method($rawReview[$field]);
                }
            }
        }

        return $reviewDb;
    }
}
