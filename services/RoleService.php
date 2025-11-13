<?php

namespace App\Services;

use App\Models\Role;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\DuplicateException;
use App\Exceptions\NotFoundException;


class RoleService
{


    public function getRoles()
    {
        return Role::getAll();
    }

    public function getRole($id)
    {
        $role = Role::findById($id);
        if (!$role) {
            throw new NotFoundException("The role was not found or not exists");
        }
        return $role;
    }

    public function getRolesName()
    {
        $role = Role::getIdAndName();
        if (!$role) {
            throw new NotFoundException("The role was not found or not exists");
        }
        return $role;
    }


    public function getRoleByActive($active)
    {
        $roles = Role::findByActive($active);
        if (!$roles) {
            throw new NotFoundException("The role was not found or not exists");
        }
        return $roles;
    }


    public function saveRole($method, $rawRole)
    {
        if ($method === "POST") {
            if (Role::findByName($rawRole["name"])) {
                throw new DuplicateException("The role name already exists");
            }

            $role = new Role($rawRole);

            if (!Role::insert($role)) {
                throw new InsertException("Failed to insert role with ID " . $role->getId());
            }

            return $role;
        } else {
            $roleDb = Role::findById($rawRole["id"]);

            if (!$roleDb) {
                throw new NotFoundException("The role was not found or not exists");
            }
            $roleNameDB = Role::findByName($rawRole["name"]);

            if ($roleNameDB && $roleNameDB->getId() !== $roleDb->getId()) {
                throw new DuplicateException("The role name already exists");
            }

            $role = $this->set($roleDb, $rawRole);

            if (!Role::edit($role)) {
                throw new UpdateException("Failed to update role with ID " . $role->getId());
            }
            return $role;
        }
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

    public function deleteRole($roleId)
    {
        if (!Role::findById($roleId)) {
            throw new NotFoundException("The role was not found or not exists");
        }

        if (!Role::delete($roleId)) {
            throw new DeleteException("Failed to delete role with ID $roleId.");
        }
    }
}
