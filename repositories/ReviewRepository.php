<?php

namespace App\Repositories;

use App\Models\Review;
use App\Utils\DatabaseHelper;
use App\Utils\PaginationHelper;
use App\Repositories\Contracts\ReviewRepositoryInterface;

class ReviewRepository implements ReviewRepositoryInterface
{
    public function findAll(): array
    {
        $rows = DatabaseHelper::query("SELECT * FROM reviews");
        $reviews = [];
        foreach ($rows as $review) {
            $reviews[] = new Review($review);
        }
        return $reviews;
    }
    public function findActive(): array
    {
        $rows = DatabaseHelper::query("SELECT * FROM reviews WHERE active = 1");
        $reviews = [];
        foreach ($rows as $review) {
            $reviews[] = new Review($review);
        }
        return $reviews;
    }

    public function findById(int $id): ?Review
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM reviews WHERE id = ?", "i", $id);
        return !empty($data) ? new Review($data[0]) : null;
    }
    public function findByProductId(int $id): array
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM reviews WHERE product_id = ?", "i", $id);
        $reviews = [];
        foreach ($data as $review) {
            $reviews[] = new Review($review);
        }
        return $reviews;
    }
    public function findByUserId(int $id): array
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM reviews WHERE user_id = ?", "i", $id);
        $reviews = [];
        foreach ($data as $review) {
            $reviews[] = new Review($review);
        }
        return $reviews;
    }

    public function findByActive(int $active): array
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM reviews WHERE active = ?", "i", $active);
        $reviews = [];
        foreach ($data as $review) {
            $reviews[] = new Review($review);
        }
        return $reviews;
    }
    public function findByProductIdAndUserId(int $productId, int $userId): ?Review
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM reviews WHERE user_id=? AND product_id=?", "ii", $userId, $productId);
        return !empty($data) ? new Review($data[0]) : null;
    }

    public function paginate(array $params): array
    {
        $tableName = "reviews";
        $search = $params['search']['value'] ?? '';
        $columns = ['id', 'name', 'description', 'active'];
        $data = PaginationHelper::make($params, $tableName, $columns);


        $filteredRecords = PaginationHelper::getFilteredCount($search, $tableName, $columns);
        $totalRecords = PaginationHelper::getTotalRecords($tableName);

        $paginated = [
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];
        return $paginated;
    }

    public function paginateDetailed(array $params): array
    {
        $selectFields = [
            'r.id',
            'r.user_id',
            'r.product_id',
            'r.rating',
            'r.title',
            'r.comment',
            'r.created_at',
            'r.updated_at',
            'r.active',
            'u.username AS username',
            'p.name AS product_name'
        ];

        $columns = [
            'r.id',
            'u.username',
            'p.name',
            'r.rating',
            'r.title',
            'r.comment',
            'r.created_at',
            'r.updated_at',
            'r.active',
            'r.user_id',
            'r.product_id',
        ];

        $fromClause = 'reviews r JOIN users u ON r.user_id = u.id JOIN products p ON r.product_id = p.id';
        $search = $params['search']['value'] ?? '';

        $data = PaginationHelper::makeCustom($params, $fromClause, $columns, $selectFields);
        $totalRecords = PaginationHelper::getTotalRecordsCustom($fromClause);
        $filteredRecords = PaginationHelper::getFilteredCustomCount($search, $fromClause, $columns);


        $paginated = [
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];
        return $paginated;
    }

    public function insert(Review $review): Review
    {
        DatabaseHelper::preparedQuery(
            "INSERT INTO reviews (product_id, user_id, title, comment, rating, created_at, updated_at, active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
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

        $review->setId(DatabaseHelper::getLastId());
        return $review;
    }

    public function update(Review $review): Review
    {
        DatabaseHelper::preparedQuery(
            "UPDATE reviews SET product_id=?, user_id=?, title=?, comment=?, rating=?, created_at=?, updated_at=?, active=? WHERE id=?",
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

        return $review;
    }
    public function delete(int $id): bool
    {
        return DatabaseHelper::preparedQuery("DELETE FROM reviews WHERE id = ?", "i", $id);
    }
}
