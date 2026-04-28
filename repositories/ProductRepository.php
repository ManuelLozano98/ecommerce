<?php

namespace App\Repositories;

use App\Models\Product;
use App\Utils\DatabaseHelper;
use App\Utils\PaginationHelper;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{

    public function findAll(): array
    {
        $rows = DatabaseHelper::query("SELECT * FROM products");
        $products = [];
        foreach ($rows as $product) {
            $products[] = new Product($product);
        }
        return $products;
    }
    public function findActive(): array
    {
        $rows = DatabaseHelper::query("SELECT * FROM products WHERE active = 1");
        $products = [];
        foreach ($rows as $product) {
            $products[] = new Product($product);
        }
        return $products;
    }

    public function findById(int $id): ?Product
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM products WHERE id = ?", "i", $id);
        return !empty($data) ? new Product($data[0]) : null;
    }
    public function findByName(string $name): ?Product
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM products WHERE name = ?", "s", $name);
        return !empty($data) ? new Product($data[0]) : null;
    }

    public function findByCode(string $code): ?Product
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM products WHERE code = ?", "s", $code);
        return !empty($data) ? new Product($data[0]) : null;
    }
    public function findByActive(int $active): array
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM products WHERE active = ?", "i", $active);
        $products = [];
        foreach ($data as $product) {
            $products[] = new Product($product);
        }
        return $products;
    }
    public function findBySlug(string $slug): ?Product
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM products WHERE slug = ?", "s", $slug);
        return !empty($data) ? new Product($data[0]) : null;
    }

    public function findByCategory(int $categoryId): array
    {
        $data = DatabaseHelper::getDatapreparedQuery("SELECT * FROM products WHERE category_id = ?", "i", $categoryId);
        $products = [];
        foreach ($data as $product) {
            $products[] = new Product($product);
        }
        return $products;
    }
    public function findActiveByCategoryPaginated(string $category, int $limit, int $offset): array
    {
        $data = DatabaseHelper::getDataPreparedQuery(
            "SELECT p.* FROM products p INNER JOIN categories c ON c.id = p.category_id WHERE p.active = 1 AND c.active = 1 AND c.name = ? LIMIT ? OFFSET ?",
            "sii",
            $category,
            $limit,
            $offset
        );
        $products = [];
        foreach ($data as $product) {
            $products[] = new Product($product);
        }
        return $products;
    }
    public function findActivePaginated(int $limit, int $offset): array
    {
        $data = DatabaseHelper::getDataPreparedQuery(
            "SELECT * FROM products WHERE active = 1 LIMIT ? OFFSET ?",
            "ii",
            $limit,
            $offset
        );
        $products = [];
        foreach ($data as $product) {
            $products[] = new Product($product);
        }
        return $products;
    }
    public function findByMinReviewScore(int $score): array
    {
        $data = DatabaseHelper::getDataPreparedQuery(
            "SELECT p.* FROM products p
        JOIN reviews r ON p.id = r.product_id
        GROUP BY p.id
        HAVING AVG(r.rating) >= ?",
            "i",
            $score
        );
        $products = [];
        foreach ($data as $product) {
            $products[] = new Product($product);
        }
        return $products;
    }
    public function findByPriceRange(?float $min, ?float $max, string $order = "ASC"): array
    {
        $sql = "SELECT * FROM products WHERE active = 1";
        $params = [];
        $types = "";

        if ($min !== null) {
            $sql .= " AND price >= ?";
            $params[] = $min;
            $types .= "d";
        }

        if ($max !== null) {
            $sql .= " AND price < ?";
            $params[] = $max;
            $types .= "d";
        }
        $sql .= " ORDER BY price $order";
        $data = DatabaseHelper::getDataPreparedQuery($sql, $types, ...$params);
        $products = [];
        foreach ($data as $product) {
            $products[] = new Product($product);
        }
        return $products;
    }
    public function findOrderedByReviewScore(string $order): array
    {
        $data = DatabaseHelper::query(
            "SELECT p.*
                 FROM products p
                 JOIN reviews r ON r.product_id = p.id
                 GROUP BY p.id
                 ORDER BY r.rating $order"
        );
        $products = [];
        foreach ($data as $product) {
            $products[] = new Product($product);
        }
        return $products;
    }
    public function findTopSellers(): array
    {
        $data = DatabaseHelper::query(
            "SELECT p.*
                 FROM products p
                 JOIN sale_items si ON si.product_id = p.id
                 JOIN sales s ON s.id = si.sale_id
                 WHERE LOWER(s.status) = 'completed'
                 GROUP BY p.id
                 ORDER BY COUNT(*) DESC"
        );
        $products = [];
        foreach ($data as $product) {
            $products[] = new Product($product);
        }
        return $products;
    }

    public function findLowestPrice(): Product
    {
        $data = DatabaseHelper::query(
            "SELECT p.*
                 FROM products p
                 ORDER BY price ASC
                 LIMIT 1"
        );
        return !empty($data) ? new Product($data[0]) : null;
    }

    public function findHigherPrice(): Product
    {
        $data = DatabaseHelper::query(
            "SELECT p.*
                 FROM products p
                 ORDER BY price DESC
                 LIMIT 1"
        );
        return !empty($data) ? new Product($data[0]) : null;
    }

    public function applyFilters(array $filters, int $limit, int $offset): array
    {
        $params = [];
        $types = "";

        $sql = "
        SELECT 
            p.*,
            COUNT(DISTINCT si.id) as total_sales,
            AVG(DISTINCT r.rating) as avg_rating
        FROM products p
        LEFT JOIN sale_items si ON si.product_id = p.id
        LEFT JOIN sales s ON s.id = si.sale_id 
            AND LOWER(s.status) = 'completed'
        LEFT JOIN reviews r ON r.product_id = p.id
        WHERE p.active = 1
    ";

        if (!empty($filters['search'])) {
            $sql .= "AND (name LIKE ? OR description LIKE ? OR slug LIKE ?)";
            $params = [...$params, ...array_fill(0, 3, "%" . $filters['search'] . "%")];
            $types .= "sss";
        }

        if (!empty($filters['categories'])) {
            $placeholders = implode(",", array_fill(0, count($filters['categories']), "?"));
            $sql .= " AND p.category_id IN ($placeholders)";
            foreach ($filters['categories'] as $category) {
                $params[] = $category;
                $types .= "i";
            }
        }

        if (!empty($filters['prices']) && count($filters['prices']) === 2) {
            if ($filters['prices'][0] === $filters['prices'][1]) {
                $sql .= " AND p.price <= ?";
                $params[] = $filters['prices'][0];
                $types .= "d";
            } else {
                $sql .= " AND p.price BETWEEN ? AND ?";
                $params[] = $filters['prices'][0];
                $params[] = $filters['prices'][1];
                $types .= "dd";
            }
        }

        $sql .= " GROUP BY p.id";

        if (!empty($filters['scores'])) {
            $scoreConditions = [];
            foreach ($filters['scores'] as $score) {
                $scoreConditions[] = "(avg_rating >= ? AND avg_rating < ?)";
                $params[] = $score;
                $params[] = $score + 1;
                $types .= "dd";
            }
            $sql .= " HAVING " . implode(" OR ", $scoreConditions);
        }

        switch ($filters['sort'] ?? '') {

            case 'top_sellers':
                $sql .= " ORDER BY total_sales DESC";
                break;

            case 'top_rated':
                $sql .= " ORDER BY avg_rating DESC";
                break;

            case 'price_asc':
                $sql .= " ORDER BY p.price ASC";
                break;

            case 'price_desc':
                $sql .= " ORDER BY p.price DESC";
                break;

            default:
                $sql .= " ORDER BY p.id DESC";
        }


        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

        $data = DatabaseHelper::getDataPreparedQuery($sql, $types, ...$params);

        return $data;
    }
    public function countFiltered(array $filters): int
    {
        $params = [];
        $types = "";


        $sql = "
        SELECT COUNT(*) AS total FROM (
            SELECT p.id, AVG(DISTINCT r.rating) as avg_rating
            FROM products p
            LEFT JOIN sale_items si ON si.product_id = p.id
            LEFT JOIN sales s ON s.id = si.sale_id 
                AND LOWER(s.status) = 'completed'
            LEFT JOIN reviews r ON r.product_id = p.id
            WHERE p.active = 1
    ";

        if (!empty($filters['search'])) {
            $sql .= "AND (name LIKE ? OR description LIKE ? OR slug LIKE ?)";
            $params = [...$params, ...array_fill(0, 3, "%" . $filters['search'] . "%")];
            $types .= "sss";
        }

        if (!empty($filters['categories'])) {
            $placeholders = implode(",", array_fill(0, count($filters['categories']), "?"));
            $sql .= " AND p.category_id IN ($placeholders)";
            foreach ($filters['categories'] as $category) {
                $params[] = $category;
                $types .= "i";
            }
        }

        if (!empty($filters['prices']) && count($filters['prices']) === 2) {
            $sql .= " AND p.price BETWEEN ? AND ?";
            $params[] = $filters['prices'][0];
            $params[] = $filters['prices'][1];
            $types .= "dd";
        }

        $sql .= " GROUP BY p.id";

        if (!empty($filters['scores'])) {
            $scoreConditions = [];
            foreach ($filters['scores'] as $score) {
                $scoreConditions[] = "(avg_rating >= ? AND avg_rating < ?)";
                $params[] = $score;
                $params[] = $score + 1;
                $types .= "dd";
            }
            $sql .= " HAVING " . implode(" OR ", $scoreConditions);
        }

        if (!$types) {
            return $this->countAll();
        }
        $sql .= " ) AS TMP";
        $result = DatabaseHelper::getDataPreparedQuery($sql, $types, ...$params);

        return $result[0]['total'] ?? 0;
    }



    // public function findCategory()
    public function countAll(): int
    {
        return (int) DatabaseHelper::query(
            "SELECT COUNT(*) AS total FROM products"
        )[0]['total'];
    }
    public function countNew(): int
    {
        return (int) DatabaseHelper::query(
            "SELECT COUNT(*) AS total
             FROM products
             WHERE DATE(created_at) = CURDATE()"
        )[0]['total'];
    }

    public function paginate(array $params): array
    {
        $tableName = "products";
        $search = $params['search']['value'] ?? '';
        $columns = ['id', 'name', 'description', 'code', 'image', 'stock', 'price', 'category_id', 'created_at', 'active']; //The columns must be in the same order as front end product table
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
            'p.id',
            'p.name AS product_name',
            'p.code',
            'p.description',
            'p.price',
            'p.image',
            'p.stock',
            'p.active AS active',
            'p.created_at',
            'p.slug',
            'c.name AS category_name',
            'c.id AS category_id'
        ];

        $columns = [
            'p.id',
            'p.name',
            'p.description',
            'p.code',
            'p.image',
            'p.stock',
            'p.price',
            'c.name',
            'p.created_at',
            'p.slug',
            'p.active'
        ];

        $fromClause = 'products p JOIN categories c ON p.category_id = c.id';
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

    public function insert(Product $product): Product
    {
        DatabaseHelper::preparedQuery(
            "INSERT INTO products (name, description, active, code, image, stock, price, category_id, created_at, slug)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            "ssissidiss",
            $product->getName(),
            $product->getDescription(),
            $product->getActive(),
            $product->getCode(),
            $product->getImage(),
            $product->getStock(),
            $product->getPrice(),
            $product->getCategoryId(),
            $product->getCreatedAt(),
            $product->getSlug()
        );

        $product->setId(DatabaseHelper::getLastId());
        return $product;
    }

    public function update(Product $product): Product
    {
        DatabaseHelper::preparedQuery(
            "UPDATE products SET name=?, description=?, active=?, code=?, image=?, stock=?, price=?, category_id=?, slug=? WHERE id=?",
            "ssissidisi",
            $product->getName(),
            $product->getDescription(),
            $product->getActive(),
            $product->getCode(),
            $product->getImage(),
            $product->getStock(),
            $product->getPrice(),
            $product->getCategoryId(),
            $product->getSlug(),
            $product->getId()
        );

        return $product;
    }
    public function delete(int $id): bool
    {
        return DatabaseHelper::preparedQuery("DELETE FROM products WHERE id = ?", "i", $id);
    }
}
