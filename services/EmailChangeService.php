<?php

namespace App\Services;

use App\Models\EmailChange;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\NotFoundException;
use App\Exceptions\DuplicateException;
use App\Repositories\Contracts\EmailChangeRepositoryInterface;
use DateTime;

class EmailChangeService
{
    private EmailChangeRepositoryInterface $repository;

    public function __construct(EmailChangeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->findAll();
    }

    public function get($id)
    {
        return $this->repository->findById($id);
    }

    public function getByToken($token)
    {
        return $this->repository->findByToken($token);
    }

    public function delete($id)
    {
        if (!$this->repository->delete($id)) {
            throw new DeleteException("Failed to delete email_change with ID $id.");
        }
    }

    public function save($rawEmail)
    {

        $email_change = new EmailChange($rawEmail);

        if (!$this->repository->insert($email_change)) {
            throw new InsertException("Failed to insert email_change with ID " . $email_change->getId());
        }
        return $email_change;
    }
    public function update($rawEmail)
    {
        $email_changeDb = $this->getByToken($rawEmail["token"]);

        $email_change = $this->set($email_changeDb, $rawEmail);

        if (!$this->repository->update($email_change)) {
            throw new UpdateException("Failed to update email_change with ID " . $email_changeDb->getId());
        }
        return $email_change;
    }


    public function verifyToken($token)
    {
        $email_change = $this->repository->findByToken($token);
        $date = (new DateTime('now'))->format('Y-m-d H:i:s');
        if (($email_change) && ($email_change?->isUsed() === false && $email_change?->getExpiredAt() > $date)) {
            return true;
        }
        return false;
    }


    private function set($email_change, $data)
    {
        $allowedFields = ['user_id', 'token', 'old_email', 'new_email', 'expires_at', 'used', 'created_at'];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));

                if (method_exists($email_change, $method)) {
                    $email_change->$method($data[$field]);
                }
            }
        }

        return $email_change;
    }
}
