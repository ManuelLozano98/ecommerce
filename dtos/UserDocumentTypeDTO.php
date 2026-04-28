<?php

namespace App\Dtos;

use App\Models\User;
use App\Models\DocumentType;

class UserDocumentTypeDTO
{
    public function __construct(private User $user, private DocumentType $document_type) {}

    public function toArray(): array
    {
        return array_merge(
            $this->user->toArray(),
            [
                'document' => [
                    'name' => $this->document_type->getName(),
                ]
            ]
        );
    }

    public static function from(array $users, array $document_types): array
    {
        return array_map(
            fn(User $u, DocumentType $d) => (new self($u, $d))->toArray(),
            $users,
            $document_types
        );
    }
}
