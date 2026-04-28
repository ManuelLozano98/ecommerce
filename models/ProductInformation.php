<?php

namespace App\Models;

use JsonSerializable;

class ProductInformation implements JsonSerializable
{
    private int $id;
    private int $productId;
    private ?string $brand;
    private ?string $manufacturer;
    private ?string $model;
    private ?string $dimensions;
    private ?string $color;
    private ?string $weight;
    private ?string $material;
    private ?string $warranty;
    private ?string $releaseDate;
    private ?string $expirationDate;
    private ?array $packageContents;
    private ?array $features;
    private ?float $ratingAverage;
    private ?array $tags;
    private ?float $discount;
    private ?array $colorOptions;
    private ?array $sizeOptions;
    private ?bool $isFeatured;
    private ?string $createdAt;
    private ?string $updatedAt;
    private ?array $technicalDetails;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? 0;
        $this->productId = $data['product_id'] ?? 0;
        $this->brand = $data['brand'] ?? null;
        $this->manufacturer = $data['manufacturer'] ?? null;
        $this->model = $data['model'] ?? null;
        $this->dimensions = $data['dimensions'] ?? null;
        $this->color = $data['color'] ?? null;
        $this->weight = $data['weight'] ?? null;
        $this->material = $data['material'] ?? null;
        $this->warranty = $data['warranty'] ?? null;
        $this->releaseDate = $data['release_date'] ?? null;
        $this->expirationDate = $data['expiration_date'] ?? null;
        $this->packageContents = $data['package_contents'] ??  null;
        $this->features = $data['features'] ?? [];
        $this->ratingAverage = $data['rating_average'] ?? 0.0;
        $this->tags = $data['tags'] ?? [];
        $this->discount = $data['discount'] ?? 0.0;
        $this->colorOptions = $data['color_options'] ?? [];
        $this->sizeOptions = $data['size_options'] ?? [];
        $this->isFeatured = $data['is_featured'] ?? false;
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
        $this->technicalDetails = $data['technical_details'] ?? [];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'product_id' => $this->productId,
            'brand' => $this->brand,
            'manufacturer' => $this->manufacturer,
            'model' => $this->model,
            'dimensions' => $this->dimensions,
            'color' => $this->color,
            'weight' => $this->weight,
            'material' => $this->material,
            'warranty' => $this->warranty,
            'release_date' => $this->releaseDate,
            'expiration_date' => $this->expirationDate,
            'package_contents' => $this->packageContents,
            'features' => $this->features,
            'rating_average' => $this->ratingAverage,
            'tags' => $this->tags,
            'discount' => $this->discount,
            'color_options' => $this->colorOptions,
            'size_options' => $this->sizeOptions,
            'is_featured' => $this->isFeatured,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'technical_details' => $this->technicalDetails,
        ];
    }


    public function getId(): int
    {
        return $this->id;
    }
    public function getProductId(): int
    {
        return $this->productId;
    }
    public function getBrand(): ?string
    {
        return $this->brand;
    }
    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }
    public function getModel(): ?string
    {
        return $this->model;
    }
    public function getDimensions(): ?string
    {
        return $this->dimensions;
    }
    public function getColor(): ?string
    {
        return $this->color;
    }
    public function getWeight(): ?string
    {
        return $this->weight;
    }
    public function getMaterial(): ?string
    {
        return $this->material;
    }
    public function getWarranty(): ?string
    {
        return $this->warranty;
    }
    public function getReleaseDate(): ?string
    {
        return $this->releaseDate;
    }
    public function getExpirationDate(): ?string
    {
        return $this->expirationDate;
    }
    public function getPackageContents(): ?array
    {
        return $this->packageContents;
    }
    public function getIsFeatured(): bool
    {
        return $this->isFeatured;
    }
    public function getFeatures(): ?array
    {
        return $this->features;
    }
    public function getRatingAverage(): float
    {
        return $this->ratingAverage;
    }
    public function getTags(): ?array
    {
        return $this->tags;
    }
    public function getDiscount(): float
    {
        return $this->discount;
    }
    public function getColorOptions(): ?array
    {
        return $this->colorOptions;
    }
    public function getSizeOptions(): ?array
    {
        return $this->sizeOptions;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }
    public function getTechnicalDetails(): ?array
    {
        return $this->technicalDetails;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setProductId(int $productId): void
    {
        $this->productId = $productId;
    }
    public function setBrand(?string $brand): void
    {
        $this->brand = $brand;
    }
    public function setManufacturer(?string $manufacturer): void
    {
        $this->manufacturer = $manufacturer;
    }
    public function setModel(?string $model): void
    {
        $this->model = $model;
    }
    public function setDimensions(?string $dimensions): void
    {
        $this->dimensions = $dimensions;
    }
    public function setColor(?string $color): void
    {
        $this->color = $color;
    }
    public function setWeight(?string $weight): void
    {
        $this->weight = $weight;
    }
    public function setMaterial(?string $material): void
    {
        $this->material = $material;
    }
    public function setWarranty(?string $warranty): void
    {
        $this->warranty = $warranty;
    }
    public function setReleaseDate(?string $releaseDate): void
    {
        $this->releaseDate = $releaseDate;
    }
    public function setExpirationDate(?string $expirationDate): void
    {
        $this->expirationDate = $expirationDate;
    }
    public function setPackageContents(?array $packageContents): void
    {
        $this->packageContents = $packageContents;
    }
    public function setFeatures(?array $features): void
    {
        $this->features = $features;
    }
    public function setRatingAverage(float $ratingAverage): void
    {
        $this->ratingAverage = $ratingAverage;
    }
    public function setTags(?array $tags): void
    {
        $this->tags = $tags;
    }
    public function setDiscount(float $discount): void
    {
        $this->discount = $discount;
    }
    public function setColorOptions(?array $colorOptions): void
    {
        $this->colorOptions = $colorOptions;
    }
    public function setSizeOptions(?array $sizeOptions): void
    {
        $this->sizeOptions = $sizeOptions;
    }
    public function setIsFeatured(bool $isFeatured): void
    {
        $this->isFeatured = $isFeatured;
    }
    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
    public function setUpdatedAt(?string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
    public function setTechnicalDetails(?array $technicalDetails): void
    {
        $this->technicalDetails = $technicalDetails;
    }
}
