<?php

namespace App\Repositories;

use App\Models\ProductInformation;
use App\Utils\DatabaseHelper;
use App\Utils\PaginationHelper;
use App\Repositories\Contracts\ProductInformationRepositoryInterface;

class ProductInformationRepository implements ProductInformationRepositoryInterface
{
    public function findAll(): array
    {
        $rows = DatabaseHelper::query("SELECT * FROM products_information");
        $items = [];

        foreach ($rows as $row) {
            $items[] = new ProductInformation($this->decodeArrays($row));
        }

        return $items;
    }

    public function findById(int $id): ?ProductInformation
    {
        $data = DatabaseHelper::getDataPreparedQuery(
            "SELECT * FROM products_information WHERE id = ?",
            "i",
            $id
        );

        return !empty($data)
            ? new ProductInformation($this->decodeArrays($data[0]))
            : null;
    }

    public function findByProductId(int $productId): ?ProductInformation
    {
        $data = DatabaseHelper::getDataPreparedQuery(
            "SELECT * FROM products_information WHERE product_id = ?",
            "i",
            $productId
        );

        return !empty($data)
            ? new ProductInformation($this->decodeArrays($data[0]))
            : null;
    }

    public function findFeatured(): array
    {
        $rows = DatabaseHelper::query(
            "SELECT * FROM products_information WHERE is_featured = 1"
        );

        $items = [];
        foreach ($rows as $row) {
            $items[] = new ProductInformation($this->decodeArrays($row));
        }

        return $items;
    }

    public function findByActiveDiscount(): array
    {
        $rows = DatabaseHelper::query(
            "SELECT * FROM products_information WHERE discount > 0"
        );

        $items = [];
        foreach ($rows as $row) {
            $items[] = new ProductInformation($this->decodeArrays($row));
        }

        return $items;
    }
    public function findByTechnicalDetails(array $filters): array
    {
        if (empty($filters)) {
            return [];
        }

        $conditions = [];
        $values = [];
        $types = "";

        foreach ($filters as $key => $value) {
            $conditions[] = "JSON_UNQUOTE(JSON_EXTRACT(technical_details, ?)) = ?";
            $values[] = '$.' . $key;
            $values[] = $value;
            $types .= "ss";
        }

        $whereClause = implode(" AND ", $conditions);

        $sql = "SELECT * FROM products_information WHERE $whereClause";

        $rows = DatabaseHelper::getDataPreparedQuery(
            $sql,
            $types,
            ...$values
        );

        $items = [];

        foreach ($rows as $row) {
            $items[] = new ProductInformation($this->decodeArrays($row));
        }

        return $items;
    }
    public function paginate(array $params): array
    {
        $tableName = "products_information";
        $search = $params['search']['value'] ?? '';
        $columns = ['id', 'product_id', 'brand', 'manufacturer', 'model', 'dimensions', 'color', 'weight', 'material', 'warranty', 'release_date', 'expiration_date', 'rating_average', 'discount', 'is_featured', 'package_contents', 'features', 'tags', 'color_options', 'size_options', 'technical_details', 'created_at', 'updated_at']; //The columns must be in the same order as front end product table
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
            'pi.brand',
            'pi.manufacturer',
            'pi.model',
            'pi.dimensions',
            'pi.color',
            'pi.weight',
            'pi.material',
            'pi.warranty',
            'pi.release_date',
            'pi.expiration_date',
            'pi.rating_average',
            'pi.discount',
            'pi.is_featured',
            'pi.package_contents',
            'pi.features',
            'pi.tags',
            'pi.color_options',
            'pi.size_options',
            'pi.technical_details',
            'pi.created_at',
            'pi.updated_at',
            'p.id AS product_id'
        ];

        $columns = [
            'pi.id',
            'p.name',
            'pi.brand',
            'pi.manufacturer',
            'pi.model',
            'pi.dimensions',
            'pi.color',
            'pi.weight',
            'pi.material',
            'pi.warranty',
            'pi.release_date',
            'pi.expiration_date',
            'pi.rating_average',
            'pi.discount',
            'pi.is_featured',
            'pi.package_contents',
            'pi.features',
            'pi.tags',
            'pi.color_options',
            'pi.size_options',
            'pi.technical_details',
            'pi.created_at',
            'pi.updated_at',
        ];

        $fromClause = 'products_information pi JOIN products p ON pi.product_id = p.id';
        $search = $params['search']['value'] ?? '';

        $data = PaginationHelper::makeCustom($params, $fromClause, $columns, $selectFields);
        $totalRecords = PaginationHelper::getTotalRecordsCustom($fromClause);
        $filteredRecords = PaginationHelper::getFilteredCustomCount($search, $fromClause, $columns);
        $newData = [];
        foreach ($data as $d) {
            $newData[] = $this->decodeArrays($d);
        }
        $paginated = [
            'data' => $newData,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
        ];
        return $paginated;
    }

    public function insert(ProductInformation $productInformation): ProductInformation
    {
        DatabaseHelper::preparedQuery(
            "INSERT INTO products_information 
            (product_id, brand, manufacturer, model, dimensions, color, weight, material,
             warranty, release_date, expiration_date, package_contents, features,
             rating_average, tags, discount, color_options, size_options,
             is_featured, created_at, updated_at, technical_details)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            "issssssssssssdsdssisss",
            $productInformation->getProductId(),
            $productInformation->getBrand(),
            $productInformation->getManufacturer(),
            $productInformation->getModel(),
            $productInformation->getDimensions(),
            $productInformation->getColor(),
            $productInformation->getWeight(),
            $productInformation->getMaterial(),
            $productInformation->getWarranty(),
            $productInformation->getReleaseDate(),
            $productInformation->getExpirationDate(),
            json_encode($productInformation->getPackageContents()),
            json_encode($productInformation->getFeatures()),
            $productInformation->getRatingAverage(),
            json_encode($productInformation->getTags()),
            $productInformation->getDiscount(),
            json_encode($productInformation->getColorOptions()),
            json_encode($productInformation->getSizeOptions()),
            $productInformation->getIsFeatured(),
            $productInformation->getCreatedAt(),
            $productInformation->getUpdatedAt(),
            json_encode($productInformation->getTechnicalDetails())
        );

        $productInformation->setId(DatabaseHelper::getLastId());

        return $productInformation;
    }

    public function update(ProductInformation $productInformation): ProductInformation
    {
        DatabaseHelper::preparedQuery(
            "UPDATE products_information SET
                brand = ?, manufacturer = ?, model = ?, dimensions = ?, color = ?, 
                weight = ?, material = ?, warranty = ?, release_date = ?, expiration_date = ?, 
                package_contents = ?, features = ?, rating_average = ?, tags = ?, 
                discount = ?, color_options = ?, size_options = ?, 
                is_featured = ?, technical_details = ?
             WHERE id = ?",
            "ssssssssssssdsdssisi",
            $productInformation->getBrand(),
            $productInformation->getManufacturer(),
            $productInformation->getModel(),
            $productInformation->getDimensions(),
            $productInformation->getColor(),
            $productInformation->getWeight(),
            $productInformation->getMaterial(),
            $productInformation->getWarranty(),
            $productInformation->getReleaseDate(),
            $productInformation->getExpirationDate(),
            json_encode($productInformation->getPackageContents()),
            json_encode($productInformation->getFeatures()),
            $productInformation->getRatingAverage(),
            json_encode($productInformation->getTags()),
            $productInformation->getDiscount(),
            json_encode($productInformation->getColorOptions()),
            json_encode($productInformation->getSizeOptions()),
            $productInformation->getIsFeatured(),
            json_encode($productInformation->getTechnicalDetails()),
            $productInformation->getId()
        );

        return $productInformation;
    }

    public function delete(int $id): bool
    {
        return DatabaseHelper::preparedQuery(
            "DELETE FROM products_information WHERE id = ?",
            "i",
            $id
        );
    }

    private function decodeArrays(array $row): array
    {
        $row['package_contents'] = $row['package_contents']
            ? json_decode($row['package_contents'], true)
            : null;

        $row['features'] = $row['features']
            ? json_decode($row['features'], true)
            : null;

        $row['tags'] = $row['tags']
            ? json_decode($row['tags'], true)
            : null;

        $row['color_options'] = $row['color_options']
            ? json_decode($row['color_options'], true)
            : null;

        $row['size_options'] = $row['size_options']
            ? json_decode($row['size_options'], true)
            : null;

        $row['technical_details'] = $row['technical_details']
            ? json_decode($row['technical_details'], true)
            : null;

        return $row;
    }
}
