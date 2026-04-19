<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\NotFoundException;
use App\Exceptions\DuplicateException;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;

class CategoryService
{
    private CategoryRepositoryInterface $repository;
    private ProductRepositoryInterface $productRepository;

    public function __construct(CategoryRepositoryInterface $repository, ProductRepositoryInterface $productRepository)
    {
        $this->repository = $repository;
        $this->productRepository = $productRepository;
    }

    public function getAll()
    {
        return $this->repository->findAll();
    }

    public function getCategory($id)
    {
        $category = $this->repository->findById($id);
        if (!$category) {
            throw new NotFoundException("The category was not found or not exists");
        }
        return $category;
    }

    public function getCategoryByName($name)
    {
        $category = $this->repository->findByName($name);
        if (!$category) {
            throw new NotFoundException("The category was not found or not exists");
        }
        return $category;
    }

    public function getActive()
    {
        return $this->repository->findActive();
    }

    public function getCategoryByProduct(Product $product)
    {
        return $this->repository->findByProduct($product);
    }

    public function getCategoryBySlug($slug)
    {
        return $this->repository->findBySlug($slug);
    }

    public function paginate($params)
    {
        return $this->repository->paginate($params);
    }

    public function delete($id)
    {
        $this->getCategory($id);

        $products = $this->productRepository->findByCategory($id);
        if ($products) {
            foreach ($products as $product) {
                $this->productRepository->delete($product->getId());
            }
        }
        if (!$this->repository->delete($id)) {
            throw new DeleteException("Failed to delete category with ID $id.");
        }
    }

    public function save($rawCategory)
    {
        if ($this->repository->findByName($rawCategory["name"])) {
            throw new DuplicateException("The category name already exists");
        }

        $category = new Category($rawCategory);

        if (!$this->repository->insert($category)) {
            throw new InsertException("Failed to insert category with ID " . $category->getId());
        }
        return $category;
    }
    public function update($rawCategory)
    {
        $categoryDb = $this->getCategory($rawCategory["id"]);

        $categoryNameFound = $this->repository->findByName($rawCategory["name"]);

        if ($categoryNameFound && $categoryNameFound->getId() !== $categoryDb->getId()) {
            throw new DuplicateException("The category name already exists");
        }
        $this->set($categoryDb, $rawCategory);

        if (!$this->repository->update($categoryDb)) {
            throw new UpdateException("Failed to update category with ID " . $categoryDb->getId());
        }
        return $categoryDb;
    }

    private function set($productDb, $rawProduct)
    {
        $allowedFields = ['name', 'description', 'active', 'slug'];

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
}
