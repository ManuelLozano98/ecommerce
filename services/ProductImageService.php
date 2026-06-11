<?php

namespace App\Services;

use App\Models\ProductImage;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\NotFoundException;
use App\Exceptions\DuplicateException;
use App\Repositories\Contracts\ProductImageRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductImageService
{
    private ProductImageRepositoryInterface $repository;
    private ProductRepositoryInterface $productRepository;

    public function __construct(
        ProductImageRepositoryInterface $repository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->repository = $repository;
        $this->productRepository = $productRepository;
    }

    public function getAll()
    {
        return $this->repository->findAll();
    }

    public function getImage($id)
    {
        $image = $this->repository->findById($id);

        if (!$image) {
            throw new NotFoundException("The product image was not found or not exists");
        }

        return $image;
    }

    public function getActive()
    {
        return $this->repository->findActive();
    }

    public function getByProductId($productId)
    {
        return $this->repository->findByProductId($productId);
    }

    public function paginate($params)
    {
        return $this->repository->paginate($params);
    }
    public function paginateDetailed($params)
    {
        return $this->repository->paginateDetailed($params);
    }

    public function saveImage($image)
    {
        $isValid = $this->validateImage($image);
        if ($isValid) {
            return $this->uploadImage($image);
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
        $uploadPath = __DIR__ . '/../uploads/images/products/gallery/' . $filename;
        $relativeUploadPath = "products/gallery/" . $filename;
        $image->moveTo($uploadPath, $filename);
        return $relativeUploadPath;
    }

    public function delete($id)
    {
        $this->getImage($id);

        if (!$this->repository->delete($id)) {
            throw new DeleteException("Failed to delete product image with ID $id.");
        }
    }

    public function save($rawImage)
    {
        $product = $this->productRepository->findById($rawImage["product_id"]);
        if (!$product) {
            throw new NotFoundException("The product does not exist");
        }

        if (isset($rawImage["type"])) {
            if ($rawImage["type"] !== "gallery") {
                $existingImages = $this->repository->findByProductId($rawImage["product_id"]);
                foreach ($existingImages as $image) {
                    if ($image->getType() === $rawImage["type"]) {
                        throw new DuplicateException("This image type already exists for this product");
                    }
                }
            }
        }
        $image = $this->saveImage($rawImage["image"]);
        $rawImage["image"] = $image;
        $productImage = new ProductImage($rawImage);
        try {
            return $this->repository->insert($productImage);
        } catch (\Throwable $e) {
            throw new InsertException("Failed to insert product image with ID " . $productImage->getId());
        }
    }

    public function update($rawImage)
    {
        $imageDb = $this->getImage($rawImage["id"]);

        if (isset($rawImage["type"])) {
            if ($rawImage["type"] !== "gallery") {
                $existingImages = $this->repository->findByProductId($imageDb->getProductId());

                foreach ($existingImages as $image) {
                    if (
                        $image->getType() === $rawImage["type"] &&
                        $image->getId() !== $imageDb->getId()
                    ) {
                        throw new DuplicateException("This image type already exists for this product");
                    }
                }
            }
        }
        if (isset($rawImage["image"])) {
            $rawImage["image"] = $this->saveImage($rawImage["image"]);
        }

        $this->set($imageDb, $rawImage);

        try {
            return $this->repository->update($imageDb);
        } catch (\Throwable $e) {
            throw new UpdateException("Failed to update product image with ID " . $imageDb->getId());
        }
    }

    private function set($imageDb, $rawImage)
    {
        $allowedFields = [
            'product_id',
            'image',
            'active',
            'type',
            'updated_at'
        ];

        foreach ($allowedFields as $field) {
            if (isset($rawImage[$field])) {
                $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));

                if (method_exists($imageDb, $method)) {
                    $imageDb->$method($rawImage[$field]);
                }
            }
        }

        return $imageDb;
    }
}
