<?php

namespace App\Services;

use App\Models\Role;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\DuplicateException;
use App\Exceptions\NotFoundException;
use App\Repositories\Contracts\RoleRepositoryInterface;


class RoleService
{
    private RoleRepositoryInterface $repository;

    public function __construct(RoleRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->findAll();
    }

    public function getRole($id)
    {
        $role = $this->repository->findById($id);
        if (!$role) {
            throw new NotFoundException("The role was not found or not exists");
        }
        return $role;
    }


    public function getRoleByActive($active)
    {
        $roles = $this->repository->findByActive($active);
        if (!$roles) {
            throw new NotFoundException("The role was not found or not exists");
        }
        return $roles;
    }

    public function paginate($params){
        return $this->repository->paginate($params);
    }


    public function save($rawRole)
    {
        if ($this->repository->findByName($rawRole["name"])) {
            throw new DuplicateException("The role name already exists");
        }

        $role = new Role($rawRole);

        if (!$this->repository->insert($role)) {
            throw new InsertException("Failed to insert role with ID " . $role->getId());
        }

        return $role;
    }
    public function update($rawRole)
    {
        $roleDb = $this->repository->findById($rawRole["id"]);

        if (!$roleDb) {
            throw new NotFoundException("The role was not found or not exists");
        }
        $roleNameDB = $this->repository->findByName($rawRole["name"]);

        if ($roleNameDB && $roleNameDB->getId() !== $roleDb->getId()) {
            throw new DuplicateException("The role name already exists");
        }

        $role = $this->set($roleDb, $rawRole);

        if (!$this->repository->update($role)) {
            throw new UpdateException("Failed to update role with ID " . $role->getId());
        }
        return $role;
    }


    private function set($roleDb, $rawRole)
    {
        $allowedFields = ['name', 'description', 'active'];

        foreach ($allowedFields as $field) {
            if (isset($rawRole[$field])) {
                $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));

                if (method_exists($roleDb, $method)) {
                    $roleDb->$method($rawRole[$field]);
                }
            }
        }

        return $roleDb;
    }

    public function delete($roleId)
    {
        if (!$this->repository->findById($roleId)) {
            throw new NotFoundException("The role was not found or not exists");
        }

        if (!$this->repository->delete($roleId)) {
            throw new DeleteException("Failed to delete role with ID $roleId.");
        }
    }
}
