<?php

namespace App\Models;

use JsonSerializable;
use DateTime;

class Product implements JsonSerializable
{
    private int $id;
    private string $name;
    private string $description;
    private bool $active;
    private string $code;
    private string $image;
    private int $stock;
    private string $price;
    private int $category_id;
    private string $created_at;
    private string $slug;
    private ?Category $category = null;

    function __construct($data = [])
    {
        $this->id = $data['id'] ?? 0;
        $this->name = $data['name'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->active = (bool) ($data['active'] ?? true);
        $this->code = $data['code'] ?? '';
        $this->image = $data['image'] ?? '';
        $this->stock = $data['stock'] ?? 0;
        $this->price = $data['price'] ?? '';
        $this->category_id = $data['category_id'] ?? 0;
        $this->created_at = empty($data['created_at']) ? (new Datetime("now"))->format('Y-m-d H:i:s') : $data['created_at'];
        $this->slug = $data['slug'] ?? $this->generateSlug($this->name);
    }


    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'active' => $this->active,
            'code' => $this->code,
            'image' => $this->image,
            'price' => $this->price,
            'stock' => $this->stock,
            'category_id' => $this->category_id,
            'created_at' => $this->created_at,
            'slug' => $this->slug,

        ];
    }

    public function changeName(string $name)
    {
        $this->name = $name;
        $this->slug = $this->generateSlug($name);
    }


    private function generateSlug($name)
    {

        $slug = strtolower($name);

        $slug = preg_replace('/[áàäâã]/u', 'a', $slug);
        $slug = preg_replace('/[éèëê]/u', 'e', $slug);
        $slug = preg_replace('/[íìïî]/u', 'i', $slug);
        $slug = preg_replace('/[óòöôõ]/u', 'o', $slug);
        $slug = preg_replace('/[úùüû]/u', 'u', $slug);
        $slug = preg_replace('/[ç]/u', 'c', $slug);
        $slug = preg_replace('/[ñ]/u', 'n', $slug);
        $slug = preg_replace('/\s+/', '-', $slug);
        $slug = preg_replace('/[^a-z0-9-]/', '', $slug);

        $slug = trim($slug, '-');

        return $slug;
    }


    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }


    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }


    public function getName()
    {
        return $this->name;
    }


    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }


    public function getId()
    {
        return $this->id;
    }


    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }


    public function getCode()
    {
        return $this->code;
    }


    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    public function getStock()
    {
        return $this->stock;
    }


    public function setStock($stock)
    {
        $this->stock = $stock;

        return $this;
    }
    public function getPrice()
    {
        return $this->price;
    }


    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    public function getCategoryId()
    {
        return $this->category_id;
    }


    public function setCategoryId($category_id)
    {
        $this->category_id = $category_id;
        $this->category = null;

        return $this;
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
        $this->category_id = $category->getId();
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }


    public function getImage()
    {
        return $this->image;
    }


    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }
}
