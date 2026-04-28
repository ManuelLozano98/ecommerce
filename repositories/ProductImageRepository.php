<?php

namespace App\Repositories;

use App\Models\ProductImage;
use App\Utils\DatabaseHelper;
use App\Utils\PaginationHelper;
use App\Repositories\Contracts\ProductImageRepositoryInterface;

class ProductImageRepository implements ProductImageRepositoryInterface
{
    public function findAll(): array
    {
        $rows = DatabaseHelper::query("SELECT * FROM products_images");
        $images = [];

        foreach ($rows as $row) {
            $images[] = new ProductImage($row);
        }

        return $images;
    }

    public function findActive(): array
    {
        $rows = DatabaseHelper::query("SELECT * FROM products_images WHERE active = 1");
        $images = [];

        foreach ($rows as $row) {
            $images[] = new ProductImage($row);
        }

        return $images;
    }

    public function findById(int $id): ?ProductImage
    {
        $data = DatabaseHelper::getDataPreparedQuery(
            "SELECT * FROM products_images WHERE id = ?",
            "i",
            $id
        );

        return !empty($data) ? new ProductImage($data[0]) : null;
    }

    public function findByProductId(int $productId): array
    {
        $data = DatabaseHelper::getDataPreparedQuery(
            "SELECT * FROM products_images WHERE product_id = ?",
            "i",
            $productId
        );

        $images = [];
        foreach ($data as $row) {
            $images[] = new ProductImage($row);
        }

        return $images;
    }

    public function findByType(string $type): array
    {
        $data = DatabaseHelper::getDataPreparedQuery(
            "SELECT * FROM products_images WHERE type = ?",
            "s",
            $type
        );

        $images = [];
        foreach ($data as $row) {
            $images[] = new ProductImage($row);
        }

        return $images;
    }

    public function findByActive(int $active): array
    {
        $data = DatabaseHelper::getDataPreparedQuery(
            "SELECT * FROM products_images WHERE active = ?",
            "i",
            $active
        );

        $images = [];
        foreach ($data as $row) {
            $images[] = new ProductImage($row);
        }

        return $images;
    }

    public function paginate(array $params): array
    {
        $tableName = "products_images";
        $search = $params['search']['value'] ?? '';
        $columns = ['id', 'product_id', 'image', 'type', 'active', 'created_at', 'updated_at']; //The columns must be in the same order as front end product table
        $data = PaginationHelper::make($params, $tableName, $columns);


        $filteredRecords = PaginationHelper::getFilteredCount($search, $tableName, $columns);
        $totalRecords = PaginationHelper::getTotalRecords($tableName);
        $paginated = [
            'data' => $data,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
        ];
        return $paginated;
    }

    public function paginateDetailed(array $params): array
    {
        $selectFields = [
            'pi.id',
            'p.name AS product_name',
            'pi.image',
            'pi.type',
            'pi.active',
            'pi.created_at',
            'pi.updated_at',
            'p.id AS product_id'
        ];

        $columns = [
            'pi.id',
            'p.name',
            'pi.image',
            'pi.type',
            'pi.active',
            'pi.created_at',
            'pi.updated_at',
        ];

        $fromClause = 'products_images pi JOIN products p ON pi.product_id = p.id';
        $search = $params['search']['value'] ?? '';

        $data = PaginationHelper::makeCustom($params, $fromClause, $columns, $selectFields);
        $totalRecords = PaginationHelper::getTotalRecordsCustom($fromClause);
        $filteredRecords = PaginationHelper::getFilteredCustomCount($search, $fromClause, $columns);
        $paginated = [
            'data' => $data,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
        ];
        return $paginated;
    }


    public function insert(ProductImage $productImage): ProductImage
    {
        DatabaseHelper::preparedQuery(
            "INSERT INTO products_images (product_id, image, active, type, created_at, updated_at) 
             VALUES (?, ?, ?, ?, ?, ?)",
            "isisss",
            $productImage->getProductId(),
            $productImage->getImage(),
            $productImage->getActive(),
            $productImage->getType(),
            $productImage->getCreatedAt(),
            $productImage->getUpdatedAt()
        );

        $productImage->setId(DatabaseHelper::getLastId());

        return $productImage;
    }

    public function update(ProductImage $productImage): ProductImage
    {
        DatabaseHelper::preparedQuery(
            "UPDATE products_images 
             SET product_id = ?, image = ?, active = ?, type = ?
             WHERE id = ?",
            "isisi",
            $productImage->getProductId(),
            $productImage->getImage(),
            $productImage->getActive(),
            $productImage->getType(),
            $productImage->getId()
        );

        return $productImage;
    }

    public function delete(int $id): bool
    {
        return DatabaseHelper::preparedQuery(
            "DELETE FROM products_images WHERE id = ?",
            "i",
            $id
        );
    }
}
