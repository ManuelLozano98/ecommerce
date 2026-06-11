<?php

namespace App\Services;

use App\Models\ProductInformation;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\NotFoundException;
use App\Exceptions\DuplicateException;
use App\Repositories\Contracts\ProductInformationRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductInformationService
{
    private ProductInformationRepositoryInterface $repository;
    private ProductRepositoryInterface $productRepository;

    public function __construct(
        ProductInformationRepositoryInterface $repository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->repository = $repository;
        $this->productRepository = $productRepository;
    }

    public function getAll()
    {
        return $this->repository->findAll();
    }

    public function getById($id)
    {
        $info = $this->repository->findById($id);

        if (!$info) {
            throw new NotFoundException("The product information was not found or not exists");
        }

        return $info;
    }

    public function getByProductId($productId)
    {
        return $this->repository->findByProductId($productId);
    }

    public function getFeatured()
    {
        return $this->repository->findFeatured();
    }

    public function getWithDiscount()
    {
        return $this->repository->findByActiveDiscount();
    }
    public function paginate($params)
    {
        return $this->repository->paginate($params);
    }
    public function paginateDetailed($params)
    {
        return $this->repository->paginateDetailed($params);
    }

    public function delete($id)
    {
        $this->getById($id);

        if (!$this->repository->delete($id)) {
            throw new DeleteException("Failed to delete product information with ID $id.");
        }
    }

    public function save($rawData)
    {
        $product = $this->productRepository->findById($rawData["product_id"]);
        if (!$product) {
            throw new NotFoundException("The product does not exist");
        }

        if ($this->repository->findByProductId($rawData["product_id"])) {
            throw new DuplicateException("Product information already exists for this product");
        }

        $productInformation = new ProductInformation($rawData);

        try {
            return $this->repository->insert($productInformation);
        } catch (\Throwable $e) {
            throw new InsertException("Failed to insert product information");
        }
    }

    public function update($rawData)
    {
        $infoDb = $this->getById($rawData["id"]);

        $this->set($infoDb, $rawData);

        try {
            return $this->repository->update($infoDb);
        } catch (\Throwable $e) {
            throw new UpdateException("Failed to update product information with ID " . $infoDb->getId());
        }
    }

    private function set($infoDb, $rawData)
    {
        $allowedFields = [
            'brand',
            'manufacturer',
            'model',
            'dimensions',
            'color',
            'weight',
            'material',
            'warranty',
            'release_date',
            'expiration_date',
            'package_contents',
            'features',
            'rating_average',
            'tags',
            'discount',
            'color_options',
            'size_options',
            'is_featured',
            'updated_at',
            'technical_details',
        ];

        foreach ($allowedFields as $field) {
            if (isset($rawData[$field])) {
                $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));

                if (method_exists($infoDb, $method)) {
                    $infoDb->$method($rawData[$field]);
                }
            }
        }

        return $infoDb;
    }
}
