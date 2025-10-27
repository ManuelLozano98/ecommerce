<?php

namespace App\Services;

use App\Models\Category;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\NotFoundException;
use App\Exceptions\DuplicateException;


class CategoryService
{

    public function getCategories()
    {
        return Category::getAll();
    }

    public function getCategory($id)
    {
        $category = Category::findById($id);
        if (!$category) {
            throw new NotFoundException("The category was not found or not exists");
        }
        return $category;
    }

    public function getCategoriesName()
    {
        $category = Category::getIdAndName();
        if (!$category) {
            throw new NotFoundException("The category was not found or not exists");
        }
        return $category;
    }

    public function getActiveCategories()
    {
        $categories = Category::getAll();
        return array_filter($categories, fn($category) => $category->getActive() === 1);
    }

    public function deleteCategory($id, ProductService $productService)
    {
        if (!Category::findById($id)) {
            throw new NotFoundException("The category was not found or not exists");
        }

        $products = $productService->getProductsByCategory($id);
        if ($products) {
            foreach ($products as $product) {
               $productService->deleteProduct($product->getId());
            }
        }
        if (!Category::delete($id)) {
            throw new DeleteException("Failed to delete category with ID $id.");
        }
    }

    public function saveCategory($method, $rawCategory)
    {
        if ($method === "POST") {
            if (Category::findByName($rawCategory["name"])) {
                throw new DuplicateException("The category name already exists");
            }

            $category = new Category($rawCategory);

            if (!Category::insert($category)) {
                throw new InsertException("Failed to insert category with ID " . $category->getId());
            } else {
                return $category;
            }
        } else {
            $categoryDb = Category::findById($rawCategory["id"]);

            if (!$categoryDb) {
                throw new NotFoundException("The category was not found or not exists");
            }
            $categoryNameFound = Category::findByName($rawCategory["name"]);

            if ($categoryNameFound && $categoryNameFound->getId() !== $categoryDb->getId()) {
                throw new DuplicateException("The category name already exists");
            }

            $categoryDb->setName($rawCategory["name"]);
            $categoryDb->setDescription($rawCategory["description"] ?? $categoryDb->getDescription());
            $categoryDb->setActive($rawCategory["active"] ?? $categoryDb->getActive());

            if (!Category::edit($categoryDb)) {
                throw new UpdateException("Failed to update category with ID " . $categoryDb->getId());
            } else {
                return $categoryDb;
            }
        }
    }
}
