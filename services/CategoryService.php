<?php

namespace App\Services;

use App\Models\Category;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;


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
            return null;
        }
        return $category;
    }

    public function getCategoriesName()
    {
        $category = Category::getIdAndName();
        if (!$category) {
            return null;
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
            return null;
        }

        $products = $productService->getProductsByCategory($id);
        if ($products) {
            foreach ($products as $product) {
                $deleted = $productService->deleteProduct($product->getId());
                if (!$deleted) {
                    throw new DeleteException("Failed to delete product with ID " . $product->getId());
                }
            }
        }
        if (!Category::delete($id)) {
            throw new DeleteException("Failed to delete category with ID $id.");
        }
        return true;
    }

    public function saveCategory($method, $rawCategory)
    {
        if ($method === "POST") {
            if (Category::findByName($rawCategory["name"])) {
                return false;
            }

            $category = new Category($rawCategory);

            if (!Category::insert($category)) {
                throw new InsertException("Failed to delete category with ID " . $category->getId());
            } else {
                return $category;
            }
        } else {
            $categoryDb = Category::findById($rawCategory["id"]);

            if (!$categoryDb) {
                return null;
            }
            $categoryNameFound = Category::findByName($rawCategory["name"]);

            if ($categoryNameFound && $categoryNameFound->getId() !== $categoryDb->getId()) {
                return false;
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
