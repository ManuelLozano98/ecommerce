<?php

namespace App\Models;

use App\Utils\DatabaseHelper;
use JsonSerializable;
use DateTime;

class Product implements JsonSerializable
{
    private int $id;
    private string $name;
    private string $description;
    private int $active;
    private string $code;
    private string $image;
    private int $stock;
    private string $price;
    private int $category_id;
    private string $created_at;

    function __construct($data = [])
    {
        $this->id = $data['id'] ?? 0;
        $this->name = $data['name'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->active = $data['active'] ?? 1;
        $this->code = $data['code'] ?? '';
        $this->image = $data['image'] ?? '';
        $this->stock = $data['stock'] ?? 0;
        $this->price = $data['price'] ?? '';
        $this->category_id = $data['category_id'] ?? 0;
        $this->created_at = empty($data['created_at']) ? (new Datetime("now"))->format('Y-m-d H:i:s') : $data['created_at'];
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
            'active' => $this->active,
            'created_at' => $this->created_at

        ];
    }
    public static function getAll()
    {
        $sql = "SELECT * FROM products";
        $query = DatabaseHelper::query($sql);
        $products = [];
        foreach ($query as $row) {
            $products[] = new Product($row);
        }
        return $products;
    }

    public static function getIdAndName()
    {
        $sql = "SELECT id, name FROM products";
        $data = DatabaseHelper::query($sql);
        $products = [];
        foreach ($data as $product) {
            $products[] = new Product($product);
        }
        return $products;
    }

    public static function getProductCount()
    {
        $sql = "SELECT COUNT(*) AS records FROM products";
        return DatabaseHelper::query($sql);
    }

    public static function getNewProductCount()
    {
        $sql = "SELECT COUNT(*) AS 'records'
        FROM products
        WHERE DATE(created_at) BETWEEN DATE(NOW() - INTERVAL 1 DAY) AND CURDATE()";
        $data = DatabaseHelper::query($sql);
        return $data;
    }


    public static function insert(Product $product)
    {
        $sql = "INSERT INTO products (name, description, active, code, image, stock, price, category_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $success = DatabaseHelper::preparedQuery(
            $sql,
            "ssissisis",
            $product->getName(),
            $product->getDescription(),
            $product->getActive(),
            $product->getCode(),
            $product->getImage(),
            $product->getStock(),
            $product->getPrice(),
            $product->getCategoryId(),
            $product->getCreatedAt()
        );

        if ($success) {
            $product->setId(DatabaseHelper::getLastId());
            return $product;
        }

        return false;
    }

    public static function edit(Product $product)
    {

        $sql = "UPDATE products SET name=?, description=?, active=?, code=?, image=?, stock=?, price=?, category_id=?, created_at=? WHERE id=?";
        $success = DatabaseHelper::preparedQuery(
            $sql,
            "ssissisisi",
            $product->getName(),
            $product->getDescription(),
            $product->getActive(),
            $product->getCode(),
            $product->getImage(),
            $product->getStock(),
            $product->getPrice(),
            $product->getCategoryId(),
            $product->getCreatedAt(),
            $product->getId()
        );
        return $success ? $product : false;
    }
    public static function delete($id)
    {
        $sql = "DELETE FROM products WHERE id = ?";
        return DatabaseHelper::preparedQuery($sql, "i", $id);
    }

    public static function findById($id)
    {
        $sql = "SELECT * FROM products WHERE id=?";
        $data = DatabaseHelper::getDatapreparedQuery($sql, "i", $id);
        return !empty($data) ? new Product($data[0]) : false;
    }


    public static function findByName($name)
    {
        $sql = "SELECT * FROM products WHERE name = ?";
        $data = DatabaseHelper::getDatapreparedQuery($sql, "s", $name);
        return !empty($data) ? new Product($data[0]) : false;
    }

    public static function findByCode($code)
    {
        $sql = "SELECT * FROM products WHERE code = ?";
        $data = DatabaseHelper::getDatapreparedQuery($sql, "s", $code);
        return !empty($data) ? new Product($data[0]) : false;
    }

    public static function findByActive($active)
    {
        $sql = "SELECT * FROM products WHERE active = ?";
        $query = DatabaseHelper::getDatapreparedQuery($sql, "i", $active);
        $products = [];
        foreach ($query as $product) {
            $products[] = new Product($product);
        }
        return $products;
    }

    public static function findByCategory($categoryId)
    {
        $sql = "SELECT * FROM products WHERE category_id = ?";
        $query = DatabaseHelper::getDatapreparedQuery($sql, "i", $categoryId);
        $products = [];
        foreach ($query as $product) {
            $products[] = new Product($product);
        }
        return $products;
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

        return $this;
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
