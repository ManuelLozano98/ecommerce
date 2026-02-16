<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Product;
use App\Utils\DatabaseHelper;
use App\Utils\PaginationHelper;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{

    public function findAll(): array
    {
        $rows = DatabaseHelper::query("SELECT * FROM categories");
        $categories = [];
        foreach ($rows as $category) {
            $categories[] = new Category($category);
        }
        return $categories;
    }
    public function findActive(): array
    {
        $rows = DatabaseHelper::query("SELECT * FROM categories WHERE active = 1");
        $categories = [];
        foreach ($rows as $category) {
            $categories[] = new Category($category);
        }
        return $categories;
    }

    public function findById(int $id): ?Category
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM categories WHERE id = ?", "i", $id);
        return !empty($data) ? new Category($data[0]) : null;
    }
    public function findByName(string $name): ?Category
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM categories WHERE name = ?", "s", $name);
        return !empty($data) ? new Category($data[0]) : null;
    }

    public function findBySlug(string $slug): ?Category
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM categories WHERE slug = ?", "s", $slug);
        return !empty($data) ? new Category($data[0]) : null;
    }

    public function findByActive(int $active): array
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM categories WHERE active = ?", "i", $active);
        $categories = [];
        foreach ($data as $category) {
            $categories[] = new Category($category);
        }
        return $categories;
    }

    public function findByProduct(Product $product): ?Category
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM categories WHERE id = ?", "i", $product->getCategoryId());
        return !empty($data) ? new Category($data[0]) : null;
    }

    public function paginate(array $params): array
    {
        $tableName = "categories";
        $search = $params['search']['value'] ?? '';
        $columns = ['id', 'name', 'description', 'active'];
        $data = PaginationHelper::make($params, $tableName, $columns);


        $filteredRecords = PaginationHelper::getFilteredCount($search, $tableName, $columns);
        $totalRecords = PaginationHelper::getTotalRecords($tableName);

        $payload = [
            'data' => $data,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
        ];
        return $payload;
    }

    public function insert(Category $category): Category
    {
        DatabaseHelper::preparedQuery(
            "INSERT INTO categories (name, description, active, slug) VALUES (?, ?, ?, ?)",
            "ssis",
            $category->getName(),
            $category->getDescription(),
            $category->getActive(),
            $category->getSlug()
        );

        $category->setId(DatabaseHelper::getLastId());
        return $category;
    }

    public function update(Category $category): Category
    {
        DatabaseHelper::preparedQuery(
            "UPDATE categories SET name=?, description=?, active=?, slug=? WHERE id=?",
            "ssisi",
            $category->getName(),
            $category->getDescription(),
            $category->getActive(),
            $category->getSlug(),
            $category->getId()
        );

        return $category;
    }
    public function delete(int $id): bool
    {
        return DatabaseHelper::preparedQuery("DELETE FROM categories WHERE id = ?", "i", $id);
    }
}
