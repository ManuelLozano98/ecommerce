<?php

namespace App\Models;

use App\Utils\DatabaseHelper;
use JsonSerializable;

class Category implements JsonSerializable
{
    private int $id;
    private string $name;
    private string $description;
    private bool $active;

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }


    function __construct($data = [])
    {
        $this->id = $data['id'] ?? 0;
        $this->name = $data['name'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->active = $data['active'] ?? 1;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'active' => $this->active,
        ];
    }

    public static function insert(Category $category)
    {
        $sql = "INSERT INTO categories (name, description, active) VALUES (?, ?, ?)";
        $success = DatabaseHelper::preparedQuery(
            $sql,
            "ssi",
            $category->getName(),
            $category->getDescription(),
            $category->getActive()
        );

        if ($success) {
            $category->setId(DatabaseHelper::getLastId());
            return $category;
        }

        return false;
    }

    public static function edit(Category $category)
    {

        $sql = "UPDATE categories SET name=?, description=?, active=? WHERE id=?";
        $success = DatabaseHelper::preparedQuery(
            $sql,
            "ssii",
            $category->getName(),
            $category->getDescription(),
            $category->getActive(),
            $category->getId()
        );
        return $success ? $category : false;
    }
    public static function delete($id)
    {
        $sql = "DELETE FROM categories WHERE id = ?";
        return DatabaseHelper::preparedQuery($sql, "i", $id);
    }

    public static function getAll()
    {
        $sql = "SELECT * FROM categories";
        $query = DatabaseHelper::query($sql);
        $categories = [];
        foreach ($query as $category) {
            $categories[] = new Category($category);
        }
        return $categories;
    }

    public static function getIdAndName()
    {
        $sql = "SELECT id, name FROM categories";
        $query = DatabaseHelper::query($sql);
        $categories = [];
        foreach ($query as $category) {
            $categories[] = new Category($category);
        }
        return $categories;
    }

    public static function findById($id)
    {
        $sql = "SELECT * FROM categories WHERE id=?";
        $data = DatabaseHelper::getDatapreparedQuery($sql, "i", $id);
        return !empty($data) ? new Category($data[0]) : false;
    }


    public static function findByName($name)
    {
        $sql = "SELECT * FROM categories WHERE NAME = ?";
        $data = DatabaseHelper::getDatapreparedQuery($sql, "s", $name);
        return !empty($data) ? new Category($data[0]) : false;
    }

    public static function findByActive($active)
    {
        $sql = "SELECT * FROM categories WHERE ACTIVE = ?";
        $query = DatabaseHelper::getDatapreparedQuery($sql, "i", $active);
        $categories = [];
        foreach ($query as $category) {
            $categories[] = new Category($category);
        }
        return $categories;
    }

    /**
     * Get the value of active
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set the value of active
     *
     * @return  self
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get the value of description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
