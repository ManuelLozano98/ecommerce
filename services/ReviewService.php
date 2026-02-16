<?php

namespace App\Services;

use App\Models\User;
use App\Models\Review;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\DuplicateException;
use App\Exceptions\ForeignKeyException;
use App\Exceptions\NotFoundException;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;

class ReviewService
{
    private ReviewRepositoryInterface $repository;
    private UserRepositoryInterface $user_repository;
    private ProductRepositoryInterface $product_repository;

    public function __construct(ReviewRepositoryInterface $review_repository, UserRepositoryInterface $user_repository, ProductRepositoryInterface $product_repository)
    {
        $this->repository = $review_repository;
        $this->user_repository = $user_repository;
        $this->product_repository = $product_repository;
    }
    public function getAll()
    {
        return $this->repository->findAll();
    }
    public function getActive()
    {
        return $this->repository->findActive();
    }
    public function getReview($id)
    {
        $review = $this->repository->findById($id);
        if (!$review) {
            throw new NotFoundException("The review was not found or does not exits");
        }
        return $review;
    }
    public function getReviewsByProduct($id)
    {
        $review = $this->repository->findByProductId($id);
        if (!$review) {
            throw new NotFoundException("The review was not found or does not exits");
        }
        return $review;
    }
    public function getReviewsByUser($id)
    {
        $review = $this->repository->findByUserId($id);
        if (!$review) {
            throw new NotFoundException("The review was not found or does not exits");
        }
        return $review;
    }
    public function getReviewByProductIdAndUserId($productId, $userId)
    {
        $review = $this->repository->findByProductIdAndUserId($productId, $userId);
        if (!$review) {
            throw new NotFoundException("The review was not found or does not exits");
        }
        return $review;
    }
    public function getUserReview($userId, $reviewId)
    {
        $reviews = $this->getReviewsByUser($userId);
        foreach ($reviews as $review) {
            if ($review->getId() === $reviewId) {
                return $review;
            }
        }
        throw new NotFoundException("The review was not found or does not exits");
    }
    public function getProductReview($productId, $reviewId)
    {
        $reviews = $this->getReviewsByProduct($productId);
        foreach ($reviews as $review) {
            if ($review->getId() === $reviewId) {
                return $review;
            }
        }
        throw new NotFoundException("The review was not found or does not exits");
    }
    public function getProductRatingStats($productId)
    {
        $reviews = $this->repository->findByProductId($productId);
        $reviews = array_filter($reviews, fn($review) => $review->getActive());
        if (empty($reviews)) {
            return ['average' => 0, 'total' => 0];
        }

        $totalRating = 0;
        foreach ($reviews as $review) {
            $totalRating += $review->getRating();
        }

        $averageRating = $totalRating / count($reviews);
        $totalReviews = count($reviews);

        return ['average' => round($averageRating, 1), 'total' => $totalReviews];
    }

    public function paginate($params)
    {
        return $this->repository->paginate($params);
    }

    public function paginateDetailed($params)
    {
        return $this->repository->paginateDetailed($params);
    }

    public function delete($id)
    {
        if (!$this->repository->findById($id)) {
            throw new NotFoundException("The review was not found or not exists");
        }

        if (!$this->repository->delete($id)) {
            throw new DeleteException("Failed to delete review with ID $id.");
        }
    }
    public function deleteReviewsByUser(User $user)
    {
        if (!$user) {
            throw new NotFoundException("The review was not found or not exists");
        }
        $userReviews = $this->repository->findByUserId($user->getId());
        if (!$userReviews) {
            throw new NotFoundException("The user has no reviews");
        }
        foreach ($userReviews as $review) {
            if (!$this->repository->delete($review->getId())) {
                throw new DeleteException("Failed to delete review with ID " . $review->getId());
            }
        }
    }
    public function save($rawReview)
    {
        if (!$this->user_repository->findById($rawReview["user_id"])) {
            throw new NotFoundException("The review was not found or not exists");
        }
        if (!$this->product_repository->findById($rawReview["product_id"])) {
            throw new NotFoundException("The review was not found or not exists");
        }

        if ($this->repository->findByProductIdAndUserId($rawReview["product_id"], $rawReview["user_id"])) {
            throw new DuplicateException("The user has a review of the product");
        }
        $review = new Review($rawReview);
        if (!$this->repository->insert($review)) {
            throw new InsertException("Failed to insert review");
        }
        return $review;
    }
    public function update($rawReview)
    {
        $reviewDb = $this->repository->findById($rawReview["id"]);
        if (!$reviewDb) {
            throw new NotFoundException("The review was not found or not exists");
        }
        if (!$this->product_repository->findById($rawReview["product_id"])) {
            throw new NotFoundException("The review was not found or not exists");
        }
        if (!$this->user_repository->findById($rawReview["user_id"])) {
            throw new NotFoundException("The review was not found or not exists");
        }
        $editReview = $this->set($reviewDb, $rawReview);
        if (!$this->repository->update($editReview)) {
            throw new UpdateException("Failed to update review with ID " . $reviewDb->getId());
        }
        return $editReview;
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
