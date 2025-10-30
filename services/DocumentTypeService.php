<?php

namespace App\Services;


use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\NotFoundException;
use App\Exceptions\DuplicateException;
use App\Models\DocumentType;


class DocumentTypeService
{

    public function getDocument_Types()
    {
        return DocumentType::getAll();
    }
    public function getDocument_TypeById($id)
    {
        return DocumentType::findById($id);
    }
}
