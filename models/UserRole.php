<?php

namespace App\Models;

use App\Utils\DatabaseHelper;
use JsonSerializable;

class UserRole implements JsonSerializable
{
    private int $id;
    private int $roleId;
    private int $userId;

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }


    function __construct($data = [])
    {
        $this->id = $data['id'] ?? 0;
        $this->roleId = $data['role_id'] ?? 0;
        $this->userId = $data['user_id'] ?? 0;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'role_id' => $this->roleId,
            'user_id' => $this->userId,
        ];
    }

    public static function insert(UserRole $userRole)
    {
        $sql = "INSERT INTO users_roles (role_id, user_id) VALUES (?, ?)";
        $success = DatabaseHelper::preparedQuery(
            $sql,
            "ii",
            $userRole->getRoleId(),
            $userRole->getUserId()
        );

        if ($success) {
            $userRole->setId(DatabaseHelper::getLastId());
            return $userRole;
        }

        return false;
    }

    public static function edit(UserRole $userRole)
    {

        $sql = "UPDATE users_roles SET role_id=?, user_id=? WHERE id=?";
        $success = DatabaseHelper::preparedQuery(
            $sql,
            "iii",
            $userRole->getRoleId(),
            $userRole->getUserId(),
            $userRole->getId()
        );
        return $success ? $userRole : false;
    }
    public static function delete($id)
    {
        $sql = "DELETE FROM users_roles WHERE id = ?";
        return DatabaseHelper::preparedQuery($sql, "i", $id);
    }

    public static function getAll()
    {
        $sql = "SELECT * FROM users_roles";
        $query = DatabaseHelper::query($sql);
        $userRoles = [];
        foreach ($query as $userRole) {
            $userRoles[] = new UserRole($userRole);
        }
        return $userRoles;
    }
    public static function findByUserId($userId)
    {
        $sql = "SELECT * FROM users_roles WHERE user_id=?";
        $query = DatabaseHelper::getDataPreparedQuery($sql, "i", $userId);
        $userRoles = [];
        foreach ($query as $userRole) {
            $userRoles[] = new UserRole($userRole);
        }
        return $userRoles;
    }

    public static function findByRoleId($roleId)
    {
        $sql = "SELECT * FROM users_roles WHERE role_id=?";
        $query = DatabaseHelper::getDataPreparedQuery($sql, "i", $roleId);
        $userRoles = [];
        foreach ($query as $userRole) {
            $userRoles[] = new UserRole($userRole);
        }
        return $userRoles;
    }

    public static function findByUserIdAndRoleId($userId, $roleId)
    {
        $sql = "SELECT * FROM users_roles WHERE role_id=? AND user_id=?";
        $query = DatabaseHelper::getDataPreparedQuery($sql, "ii", $roleId, $userId);
        $userRoles = [];
        foreach ($query as $userRole) {
            $userRoles[] = new UserRole($userRole);
        }
        return $userRoles;
    }

    public static function getUsersByRoleName($roleName)
    {
        $sql = "SELECT * FROM users WHERE user_id IN (
SELECT u_r.user_id FROM users_roles u_r WHERE u_r.role_id = (
    SELECT role_id FROM roles WHERE name = '?'))";
        $data = DatabaseHelper::getDataPreparedQuery($sql, "s", $roleName);
        $users = [];
        foreach ($data as $user) {
            $users[] = new User($user);
        }
        return $users;
    }

    public static function getUsersByRole(Role $role)
    {
        $sql = "SELECT * FROM users WHERE user_id IN (
SELECT u_r.user_id FROM users_roles u_r WHERE u_r.role_id = ?)";
        $data = DatabaseHelper::getDataPreparedQuery($sql, "i", $role->getId());
        $users_roles = [];
        foreach ($data as $user_role) {
            $users_roles[] = new User($user_role);
        }
        return $users_roles;
    }
    public static function getRolesByUser(User $user)
    {
        $id = $user->getId();
        $sql = "SELECT r.* FROM users_roles u_r, roles r, users u WHERE r.role_id = u_r.id AND u_r.user_id = ? AND u.user_id = ?";
        $data = DatabaseHelper::getDataPreparedQuery($sql, "ii", $id, $id);
        $roles = [];
        foreach ($data as $user_role) {
            $roles[] = new Role($user_role);
        }
        return $roles;
    }

    public static function hasRole(User $user, $rolename)
    {
        $sql = "SELECT id FROM users_roles WHERE user_id = ? AND role_id = (SELECT role_id FROM roles WHERE name = ?)";
        $data = DatabaseHelper::getDataPreparedQuery($sql, "is", $user->getId(), $rolename);
        return !empty($data);
    }
    public static function isAdmin(User $user)
    {
        return self::hasRole($user, "admin");
    }

    public static function findById($id)
    {
        $sql = "SELECT * FROM users_roles WHERE id=?";
        $data = DatabaseHelper::getDatapreparedQuery($sql, "i", $id);
        return !empty($data) ? new UserRole($data[0]) : false;
    }


    public function getRoleId()
    {
        return $this->roleId;
    }
    public function setRoleId($roleId)
    {
        $this->roleId = $roleId;

        return $this;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }


    public function getUserId()
    {
        return $this->userId;
    }


    public function getId()
    {
        return $this->id;
    }


    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
