<?php

namespace App\Models;

use App\Utils\DatabaseHelper;
use JsonSerializable;

class DocumentType implements JsonSerializable
{
    private int $id;
    private string $name;

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }


    function __construct($data = [])
    {
        $this->id = $data['id'] ?? 0;
        $this->name = $data['name'] ?? '';
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    public static function insert(DocumentType $documentType)
    {
        $sql = "INSERT INTO document_types (name) VALUES (?)";
        $success = DatabaseHelper::preparedQuery(
            $sql,
            "s",
            $documentType->getName(),
        );

        if ($success) {
            $documentType->setId(DatabaseHelper::getLastId());
            return $documentType;
        }

        return false;
    }

    public static function edit(DocumentType $documentType)
    {

        $sql = "UPDATE document_types SET name=? WHERE id=?";
        $success = DatabaseHelper::preparedQuery(
            $sql,
            "si",
            $documentType->getName(),
            $documentType->getId()
        );
        return $success ? $documentType : false;
    }
    public static function delete($id)
    {
        $sql = "DELETE FROM document_types WHERE id = ?";
        return DatabaseHelper::preparedQuery($sql, "i", $id);
    }

    public static function getAll()
    {
        $sql = "SELECT * FROM document_types";
        $query = DatabaseHelper::query($sql);
        $document_types = [];
        foreach ($query as $documentType) {
            $document_types[] = new DocumentType($documentType);
        }
        return $document_types;
    }

    public static function findById($id)
    {
        $sql = "SELECT * FROM document_types WHERE id=?";
        $data = DatabaseHelper::getDatapreparedQuery($sql, "i", $id);
        return !empty($data) ? new DocumentType($data[0]) : false;
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
