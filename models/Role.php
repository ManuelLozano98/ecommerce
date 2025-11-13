<?php

namespace App\Models;

use App\Utils\DatabaseHelper;
use JsonSerializable;

class Role implements JsonSerializable
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

    public static function insert(Role $role)
    {
        $sql = "INSERT INTO roles (name, description, active) VALUES (?, ?, ?)";
        $success = DatabaseHelper::preparedQuery(
            $sql,
            "ssi",
            $role->getName(),
            $role->getDescription(),
            $role->getActive()
        );

        if ($success) {
            $role->setId(DatabaseHelper::getLastId());
            return $role;
        }

        return false;
    }

    public static function edit(Role $role)
    {

        $sql = "UPDATE roles SET name=?, description=?, active=? WHERE id=?";
        $success = DatabaseHelper::preparedQuery(
            $sql,
            "ssii",
            $role->getName(),
            $role->getDescription(),
            $role->getActive(),
            $role->getId()
        );
        return $success ? $role : false;
    }
    public static function delete($id)
    {
        $sql = "DELETE FROM roles WHERE id = ?";
        return DatabaseHelper::preparedQuery($sql, "i", $id);
    }

    public static function getAll()
    {
        $sql = "SELECT * FROM roles";
        $query = DatabaseHelper::query($sql);
        $roles = [];
        foreach ($query as $role) {
            $roles[] = new Role($role);
        }
        return $roles;
    }

    public static function getIdAndName()
    {
        $sql = "SELECT id, name FROM roles";
        $query = DatabaseHelper::query($sql);
        $roles = [];
        foreach ($query as $role) {
            $roles[] = new Role($role);
        }
        return $roles;
    }

    public static function findById($id)
    {
        $sql = "SELECT * FROM roles WHERE id=?";
        $data = DatabaseHelper::getDatapreparedQuery($sql, "i", $id);
        return !empty($data) ? new Role($data[0]) : false;
    }


    public static function findByName($name)
    {
        $sql = "SELECT * FROM roles WHERE NAME = ?";
        $data = DatabaseHelper::getDatapreparedQuery($sql, "s", $name);
        return !empty($data) ? new Role($data[0]) : false;
    }

    public static function findByActive($active)
    {
        $sql = "SELECT * FROM roles WHERE ACTIVE = ?";
        $query = DatabaseHelper::getDatapreparedQuery($sql, "i", $active);
        $roles = [];
        foreach ($query as $role) {
            $roles[] = new Role($role);
        }
        return $roles;
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
}
