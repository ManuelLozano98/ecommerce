<?php

namespace App\Repositories;

use App\Utils\DatabaseHelper;
use App\Repositories\Contracts\DocumentTypeRepositoryInterface;
use App\Models\DocumentType;

class DocumentTypeRepository implements DocumentTypeRepositoryInterface
{

    public function findAll(): array
    {
        $rows = DatabaseHelper::query("SELECT * FROM document_types");
        $documents = [];
        foreach ($rows as $document) {
            $documents[] = new DocumentType($document);
        }
        return $documents;
    }

    public function findById(int $id): ?DocumentType
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM document_types WHERE id = ?", "i", $id);
        return !empty($data) ? new DocumentType($data[0]) : null;
    }

    public function save(DocumentType $documentType): DocumentType
    {
        DatabaseHelper::preparedQuery("INSERT INTO document_types (name) VALUES (?)", "s", $documentType->getName());
        $documentType->setId(DatabaseHelper::getLastId());
        return $documentType;
    }

    public function update(DocumentType $documentType): DocumentType
    {

        DatabaseHelper::preparedQuery(
            "UPDATE document_types SET name=? WHERE id=?",
            "si",
            $documentType->getName(),
            $documentType->getId()
        );
        return $documentType;
    }
    public function delete($id): bool
    {
        return DatabaseHelper::preparedQuery("DELETE FROM document_types WHERE id = ?", "i", $id);
    }
}
