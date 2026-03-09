<?php

namespace App\Models;

class ProductImage
{
    private int $id;
    private int $productId;
    private string $image;
    private bool $active;
    private string $type;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? 0;
        $this->productId = $data['product_id'] ?? 0;
        $this->image = $data['image'] ?? '';
        $this->active = $data['active'] ?? true;
        $this->type = $data['type'] ?? 'gallery';
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function getProductId(): int
    {
        return $this->productId;
    }
    public function getImage(): string
    {
        return $this->image;
    }

    public function getType(): string
    {
        return $this->type;
    }
    public function getActive(): bool
    {
        return $this->active;
    }
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setProductId(int $id): void
    {
        $this->productId = $id;
    }
    public function setImage(string $image): void
    {
        $this->image = $image;
    }
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
