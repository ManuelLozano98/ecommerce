<?php

namespace App\Services;

use App\Services\CategoryService;
use App\Models\Product;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\DuplicateException;
use App\Exceptions\ForeignKeyException;
use App\Exceptions\NotFoundException;


class ProductService
{
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function getProducts()
    {
        return Product::getAll();
    }

    public function getProduct($id)
    {
        $product = Product::findById($id);
        if (!$product) {
            throw new NotFoundException("The product was not found or not exists");
        }
        return $product;
    }

    public function getProductsName()
    {
        $product = Product::getIdAndName();
        if (!$product) {
            throw new NotFoundException("The product was not found or not exists");
        }
        return $product;
    }

    public function getProductByCode($code)
    {
        $product = Product::findByCode($code);
        if (!$product) {
            throw new NotFoundException("The product was not found or not exists");
        }
        return $product;
    }

    public function getProductByActive($active)
    {
        $products = Product::findByActive($active);
        if (!$products) {
            throw new NotFoundException("The product was not found or not exists");
        }
        return $products;
    }


    public function saveProduct($method, $rawProduct)
    {
        if ($method === "POST") {
            if (Product::findByName($rawProduct["name"])) {
                throw new DuplicateException("The product name already exists");
            }
            if (Product::findByCode($rawProduct["code"])) {
                throw new DuplicateException("The product code already exists " . $rawProduct["code"]);
            }
            if (!$this->categoryService->getCategory($rawProduct["category_id"])) {
                throw new InsertException("Failed to add product with category ID " . $rawProduct["category_id"]);
            }

            $product = new Product($rawProduct);

            if (!Product::insert($product)) {
                throw new InsertException("Failed to insert product with ID " . $product->getId());
            }

            return $product;
        } else {
            $productDb = Product::findById($rawProduct["id"]);

            if (!$productDb) {
                throw new NotFoundException("The product was not found or not exists");
            }
            $productNameDB = Product::findByName($rawProduct["name"]);

            if ($productNameDB && $productNameDB->getId() !== $productDb->getId()) {
                throw new DuplicateException("The product name already exists");
            }

            $productCodeDB = Product::findByCode($rawProduct["code"]);

            if ($productCodeDB && $productCodeDB->getId() !== $productDb->getId()) {
                throw new DuplicateException("The product code already exists " . $rawProduct["code"]);
            }
            if (!$this->categoryService->getCategory($rawProduct["category_id"])) {
                throw new ForeignKeyException("The category was not found or not exists " . $rawProduct["category_id"]);
            }
            $product = $this->set($productDb, $rawProduct);

            if (!Product::edit($product)) {
                throw new UpdateException("Failed to update product with ID " . $product->getId());
            }
            return $product;
        }
    }

    private function set($productDb, $rawProduct)
    {
        $allowedFields = ['name', 'description', 'active', 'code', 'image', 'stock', 'price', 'category_id', 'created_at'];

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
        $products = Product::findByCategory($categoryId);
        if (!$products) {
            throw new NotFoundException("The product was not found or not exists");
        }
        return $products;
    }

    public function deleteProduct($productId)
    {
        if (!Product::findById($productId)) {
            throw new NotFoundException("The product was not found or not exists");
        }

        if (!Product::delete($productId)) {
            throw new DeleteException("Failed to delete product with ID $productId.");
        }
    }
}
