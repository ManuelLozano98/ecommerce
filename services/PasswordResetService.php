<?php

namespace App\Services;

use App\Models\PasswordReset;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\NotFoundException;
use App\Exceptions\DuplicateException;
use App\Repositories\Contracts\PasswordRepositoryInterface;

class PasswordResetService
{
    private PasswordRepositoryInterface $repository;

    public function __construct(PasswordRepositoryInterface $repository)
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
            throw new DeleteException("Failed to delete password with ID $id.");
        }
    }

    public function save($rawPassword)
    {

        $password = new PasswordReset($rawPassword);

        try {
            return $this->repository->insert($password);
        } catch (\Throwable $e) {
            throw new InsertException("Failed to insert password reset");
        }
    }
    public function update($rawPassword)
    {
        $passwordDb = $this->getByToken($rawPassword["token"]);

        $this->set($passwordDb, $rawPassword);

        try {
            return $this->repository->update($passwordDb);
        } catch (\Throwable $e) {
            throw new UpdateException("Failed to update password with ID " . $passwordDb->getId());
        }
    }


    private function set($password, $data)
    {
        $allowedFields = ['user_id', 'token', 'expires_at', 'used', 'created_at'];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));

                if (method_exists($password, $method)) {
                    $password->$method($data[$field]);
                }
            }
        }

        return $password;
    }
}
