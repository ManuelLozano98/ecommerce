<?php

namespace App\Services;


use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\NotFoundException;
use App\Exceptions\DuplicateException;
use App\Models\DocumentType;
use App\Repositories\Contracts\DocumentTypeRepositoryInterface;


class DocumentTypeService
{
    private DocumentTypeRepositoryInterface $document;

    public function __construct(DocumentTypeRepositoryInterface $document)
    {
        $this->document = $document;
    }

    public function getAll()
    {
        return $this->document->findAll();
    }
    public function getById($id)
    {
        return $this->document->findById($id);
    }

}
