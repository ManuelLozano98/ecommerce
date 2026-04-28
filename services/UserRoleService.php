<?php

namespace App\Services;

use App\Models\UserRole;
use App\Models\Role;
use App\Models\User;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\DuplicateException;
use App\Exceptions\ForeignKeyException;
use App\Exceptions\NotFoundException;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\UserRoleRepositoryInterface;


class UserRoleService
{
    private UserRoleRepositoryInterface $userRoleRepository;
    private UserRepositoryInterface $userRepository;
    private RoleRepositoryInterface $roleRepository;

    public function __construct(UserRoleRepositoryInterface $userRoleRepository, UserRepositoryInterface $userRepository, RoleRepositoryInterface $roleRepository)
    {
        $this->userRoleRepository = $userRoleRepository;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    public function getUserRoles()
    {
        return $this->userRoleRepository->findAll();
    }
    public function getUserswithRole(Role $role)
    {
        return $this->userRoleRepository->findByRoleId($role->getId());
    }

    public function getUserRoleByRole($roleId)
    {
        return $this->userRoleRepository->findByRoleId($roleId);
    }

    public function getUserRoleById($id)
    {
        $userRole = $this->userRoleRepository->findById($id);
        if (!$userRole) {
            throw new NotFoundException("The userRole was not found or not exists");
        }
        return $userRole;
    }
    public function getUserRolesbyUserId($userId)
    {
        return $this->userRoleRepository->findByUserId($userId);
    }

    public function getUserRolebyUserIdAndRoleId($userId, $roleId)
    {
        $usersRoles = $this->userRoleRepository->findByUserIdAndRoleId($userId, $roleId);
        if (!$usersRoles) {
            throw new NotFoundException("The user was not found or not exists");
        }
        return $usersRoles;
    }

    public function getUserRolesDetailed()
    {
        $usersRoles = $this->userRoleRepository->findAll();
        $data = [];
        foreach ($usersRoles as $key => $value) {
            $data[$key] = $value->toArray();
            $data[$key]["username"] = $this->userRepository->findById($value->getUserId())->getUsername();
            $data[$key]["name"] = $this->roleRepository->findById($value->getRoleId())->getName();
        }
        $result = [];
        foreach ($data as $entry) {
            $user = $entry["username"];
            if (!isset($result[$user])) {
                $result[$user] = [
                    "user_id" => $entry["user_id"],
                    "username" => $entry["username"],
                    "roles" => []
                ];
            }
            $result[$user]["roles"][] = [
                "id" => $entry["id"],
                "role_id" => $entry["role_id"],
                "name" => $entry["name"]
            ];
        }
        return $result;
    }


    public function save($rawUserRole)
    {
        if (!$this->userRepository->findById($rawUserRole["user_id"])) {
            throw new NotFoundException("The user was not found or not exists");
        }
        if (!$this->roleRepository->findById($rawUserRole["role_id"])) {
            throw new NotFoundException("The role was not found or not exists");
        }
        if ($this->userRoleRepository->findByUserIdAndRoleId($rawUserRole["user_id"], $rawUserRole["role_id"])) {
            throw new DuplicateException("The user already has the role");
        }

        $userRole = new UserRole($rawUserRole);

        if (!$this->userRoleRepository->insert($userRole)) {
            throw new InsertException("Failed to insert userRole with ID " . $userRole->getId());
        }

        return $userRole;
    }
    public function update($rawUserRole)
    {
        $userRoleDb = $this->userRoleRepository->findById($rawUserRole["id"]);

        if (!$userRoleDb) {
            throw new NotFoundException("The userRole was not found or not exists");
        }
        $canEdit = $this->userRoleRepository->findByUserIdAndRoleId($rawUserRole["user_id"], $rawUserRole["role_id"])[0];
        if ($canEdit && $userRoleDb->getId() !== $canEdit->getId()) {
            throw new DuplicateException("The user already has the role");
        }

        $userRole = $this->set($userRoleDb, $rawUserRole);

        if (!$this->userRoleRepository->update($userRole)) {
            throw new UpdateException("Failed to update userRole with ID " . $userRole->getId());
        }
        return $userRole;
    }


    private function set($userRoleDb, $rawUserRole)
    {
        $allowedFields = ['user_id', 'role_id'];

        foreach ($allowedFields as $field) {
            if (isset($rawUserRole[$field])) {
                $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));

                if (method_exists($userRoleDb, $method)) {
                    $userRoleDb->$method($rawUserRole[$field]);
                }
            }
        }

        return $userRoleDb;
    }


    public function delete($userRoleId)
    {
        if (!$this->userRoleRepository->findById($userRoleId)) {
            throw new NotFoundException("The userRole was not found or not exists");
        }

        if (!$this->userRoleRepository->delete($userRoleId)) {
            throw new DeleteException("Failed to delete userRole with ID $userRoleId.");
        }
    }
}
