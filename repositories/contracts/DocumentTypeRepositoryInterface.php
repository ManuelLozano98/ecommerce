<?php

namespace App\Repositories\Contracts;

use App\Models\DocumentType;


interface DocumentTypeRepositoryInterface
{
    public function findAll(): array;

    public function findById(int $id): ?DocumentType;

    public function save(DocumentType $documentType): DocumentType;

    public function update(DocumentType $documentType): DocumentType;

    public function delete(int $id): bool;
}