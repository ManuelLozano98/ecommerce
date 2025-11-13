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
use App\Services\UserService;
use App\Services\RoleService;


class UserRoleService
{
    private UserService $userService;
    private RoleService $roleService;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->roleService = new RoleService();
    }

    public function getUserRoles()
    {
        return UserRole::getAll();
    }
    public function getUserswithRole(Role $role)
    {
        return UserRole::getUsersByRole($role);
    }
    public function getUserswithRoleName($rolename)
    {
        return UserRole::getUsersByRoleName($rolename);
    }
    public function getUserRoleByRole($roleId)
    {
        return UserRole::findByRoleId($roleId);
    }

    public function getUserRoleById($id)
    {
        $userRole = UserRole::findById($id);
        if (!$userRole) {
            throw new NotFoundException("The userRole was not found or not exists");
        }
        return $userRole;
    }
    public function getUserRolesbyUserId($userId)
    {
        $usersRoles = UserRole::findByUserId($userId);
        if (!$usersRoles) {
            throw new NotFoundException("The user was not found or not exists");
        }
        return $usersRoles;
    }

    public function getUserRolebyUserIdAndRoleId($userId, $roleId)
    {
        $usersRoles = UserRole::findByUserIdAndRoleId($userId, $roleId);
        if (!$usersRoles) {
            throw new NotFoundException("The user was not found or not exists");
        }
        return $usersRoles[0];
    }

    public function getUserRolesDetailedJSON()
    {
        $usersRoles = UserRole::getAll();
        $data = [];
        foreach ($usersRoles as $key => $value) {
            $data[$key] = $value->toArray();
            $data[$key]["username"] = $this->userService->getUser($value->getUserId())->getUsername();
            $data[$key]["name"] = $this->roleService->getRole($value->getRoleId())->getName();
        }
        $json = [];
        foreach ($data as $entry) {
            $user = $entry["username"];
            if (!isset($json[$user])) {
                $json[$user] = [
                    "user_id" => $entry["user_id"],
                    "username" => $user,
                    "roles" => []
                ];
            }
            $json[$user]["roles"][] = [
                "id" => $entry["id"],
                "role_id" => $entry["role_id"],
                "name" => $entry["name"]
            ];
        }
        $json = json_encode(["data" => array_values($json)], true);
        return $json;
    }

    public function hasAdminRole($user)
    {
        if (!$user || !$user instanceof User) {
            throw new NotFoundException("The user was not found or not exists");
        }
        return UserRole::isAdmin($user);
    }
    public function hasRole(User $user, $rolename)
    {
        return UserRole::hasRole($user, $rolename);
    }


    public function saveUserRole($method, $rawUserRole)
    {
        if ($method === "POST") {
            if (!$this->userService->getUser($rawUserRole["user_id"])) {
                throw new NotFoundException("The user was not found or not exists");
            }
            if (!$this->roleService->getRole($rawUserRole["role_id"])) {
                throw new NotFoundException("The role was not found or not exists");
            }
            if (UserRole::findByUserIdAndRoleId($rawUserRole["user_id"], $rawUserRole["role_id"])) {
                throw new DuplicateException("The user already has the role");
            }

            $userRole = new UserRole($rawUserRole);

            if (!UserRole::insert($userRole)) {
                throw new InsertException("Failed to insert userRole with ID " . $userRole->getId());
            }

            return $userRole;
        } else {
            $userRoleDb = UserRole::findById($rawUserRole["id"]);

            if (!$userRoleDb) {
                throw new NotFoundException("The userRole was not found or not exists");
            }
            $canEdit = UserRole::findByUserIdAndRoleId($rawUserRole["user_id"], $rawUserRole["role_id"])[0];
            if ($canEdit && $userRoleDb->getId() !== $canEdit->getId()) {
                throw new DuplicateException("The user already has the role");
            }

            $userRole = $this->set($userRoleDb, $rawUserRole);

            if (!UserRole::edit($userRole)) {
                throw new UpdateException("Failed to update userRole with ID " . $userRole->getId());
            }
            return $userRole;
        }
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


    public function deleteUserRole($userRoleId)
    {
        if (!UserRole::findById($userRoleId)) {
            throw new NotFoundException("The userRole was not found or not exists");
        }

        if (!UserRole::delete($userRoleId)) {
            throw new DeleteException("Failed to delete userRole with ID $userRoleId.");
        }
    }

    public function getRoleService()
    {
        return $this->roleService;
    }
    public function getUserService()
    {
        return $this->userService;
    }
}
