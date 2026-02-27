<?php

namespace App\Services;

use App\Models\Product;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\DuplicateException;
use App\Exceptions\ForeignKeyException;
use App\Exceptions\NotFoundException;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;


class ProductService
{
    private ProductRepositoryInterface $repository;
    private CategoryRepositoryInterface $category_repository;


    public function __construct(ProductRepositoryInterface $repository, CategoryRepositoryInterface $category_repository)
    {
        $this->repository = $repository;
        $this->category_repository = $category_repository;
    }

    public function getAll()
    {
        return $this->repository->findAll();
    }
    public function getActive()
    {
        return $this->repository->findActive();
    }

    public function getActivePaginated($limit, $offset)
    {
        return $this->repository->findActivePaginated($limit, $offset);
    }
    public function getActivePaginatedByCategory($category, $limit, $offset)
    {
        if ($category && $this->category_repository->findByName($category)) {
            return $this->repository->findActiveByCategoryPaginated($category, $limit, $offset);
        }
        return $this->repository->findActivePaginated($limit, $offset);
    }

    public function getProduct($id)
    {
        $product = $this->repository->findById($id);
        if (!$product) {
            throw new NotFoundException("The product was not found or not exists");
        }
        return $product;
    }


    public function getProductByName($name)
    {
        $product = $this->repository->findByName($name);
        if (!$product) {
            throw new NotFoundException("The product was not found or not exists");
        }
        return $product;
    }

    public function getProductBySlug($slug)
    {
        $product = $this->repository->findBySlug($slug);
        if (!$product) {
            throw new NotFoundException("The product was not found or not exists");
        }
        return $product;
    }

    public function getProductByCode($code)
    {
        $product = $this->repository->findByCode($code);
        if (!$product) {
            throw new NotFoundException("The product was not found or not exists");
        }
        return $product;
    }

    public function getProductByActive($active)
    {
        $products = $this->repository->findByActive($active);
        if (!$products) {
            throw new NotFoundException("The product was not found or not exists");
        }
        return $products;
    }

    public function getProductsByPriceRange($min, $max, $order = "ASC")
    {
        if ($order !== "ASC") {
            $order = "DESC";
        }

        return $this->repository->findByPriceRange($min, $max, $order);
    }

    public function getTopSellers()
    {
        return $this->repository->findTopSellers();
    }

    public function getProductsByReviewScore($score)
    {
        return $this->repository->findByMinReviewScore($score);
    }

    public function getProductsOrderedByReviewScore($order)
    {
        return $this->repository->findOrderedByReviewScore($order);
    }

    public function getLowestPrice()
    {
        return $this->repository->findLowestPrice();
    }

    public function getHigherPrice()
    {
        return $this->repository->findHigherPrice();
    }

    public function getProductsRawFiltered($filters, $limit, $offset)
    {
        return $this->repository->applyFilters($filters, $limit, $offset);
    }
    public function getProductsFiltered($filters, $limit, $offset)
    {
        $products = $this->repository->applyFilters($filters, $limit, $offset);
        $data = [];
        foreach ($products as $row) {
            $products[] = new Product($row);
        }
        return $data;
    }


    public function countFiltered($filters)
    {
        return $this->repository->countFiltered($filters);
    }

    public function paginate($params)
    {
        return $this->repository->paginate($params);
    }

    public function paginateDetailed($params)
    {
        return $this->repository->paginateDetailed($params);
    }

    public function save($rawProduct)
    {
        if ($this->repository->findByName($rawProduct["name"])) {
            throw new DuplicateException("The product name already exists");
        }
        if ($this->repository->findByCode($rawProduct["code"])) {
            throw new DuplicateException("The product code already exists " . $rawProduct["code"]);
        }
        if (!$this->category_repository->findById($rawProduct["category_id"])) {
            throw new InsertException("Failed to add product with category ID " . $rawProduct["category_id"]);
        }

        $product = new Product($rawProduct);

        if (!$this->repository->insert($product)) {
            throw new InsertException("Failed to insert product '{$product->getName()}'.");
        }

        return $product;
    }
    public function update($rawProduct)
    {
        $productDb = $this->getProduct($rawProduct['id']);

        $existingName = $this->repository->findByName($rawProduct['name']);

        if ($existingName && $existingName->getId() !== $productDb->getId()) {
            throw new DuplicateException("The product name already exists");
        }

        $existingCode = $this->repository->findByCode($rawProduct['code']);

        if ($existingCode && $existingCode->getId() !== $productDb->getId()) {
            throw new DuplicateException("The product code already exists " . $rawProduct["code"]);
        }
        if (!$this->category_repository->findById($rawProduct["category_id"])) {
            throw new ForeignKeyException("The category was not found or not exists " . $rawProduct["category_id"]);
        }

        $product = $this->set($productDb, $rawProduct);

        if (!$this->repository->update($product)) {
            throw new UpdateException("Failed to update product '{$product->getName()}'.");
        }

        return $product;
    }

    public function saveImage($image, $productId)
    {
        $isValid = $this->validateImage($image);
        if ($isValid) {
            $product = $this->getProduct($productId);

            $upload  = $this->uploadImage($image);
            $product->setImage($upload);
            $this->repository->update($product);
            return $product->getImage();
        }
    }
    private function validateImage($image)
    {
        if ($image->getError() !== UPLOAD_ERR_OK) {
            return false;
        }

        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($image->getSize() > $maxSize) {
            return false;
        }
        return true;
    }
    private function uploadImage($image)
    {
        $extension = pathinfo($image->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);
        $uploadPath = __DIR__ . '/../uploads/images/products/' . $filename;
        $relativeUploadPath = "products/" . $filename;
        $image->moveTo($uploadPath, $filename);
        return $relativeUploadPath;
    }

    private function set($productDb, $rawProduct)
    {
        $allowedFields = ['name', 'description', 'active', 'code', 'image', 'stock', 'price', 'category_id', 'created_at', 'slug'];

        foreach ($allowedFields as $field) {
            if (isset($rawProduct[$field])) {
                $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));

                if (method_exists($productDb, $method)) {
                    $productDb->$method($rawProduct[$field]);
                }
            }
        }

        return $productDb;
    }

    public function getProductsByCategory($categoryId)
    {
        return $this->repository->findByCategory($categoryId);
    }

    public function delete($productId)
    {
        $this->getProduct($productId);

        if (!$this->repository->delete($productId)) {
            throw new DeleteException("Failed to delete product with ID $productId.");
        }
    }
}
