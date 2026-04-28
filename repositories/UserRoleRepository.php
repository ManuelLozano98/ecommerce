<?php

namespace App\Repositories;

use App\Models\UserRole;
use App\Utils\DatabaseHelper;
use App\Repositories\Contracts\UserRoleRepositoryInterface;

class UserRoleRepository implements UserRoleRepositoryInterface
{

    public function findAll(): array
    {
        $rows = DatabaseHelper::query("SELECT * FROM users_roles");
        $users_roles = [];
        foreach ($rows as $user_role) {
            $users_roles[] = new UserRole($user_role);
        }
        return $users_roles;
    }

    public function findById(int $id): ?UserRole
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM users_roles WHERE id = ?", "i", $id);
        return !empty($data) ? new UserRole($data[0]) : null;
    }
    public function findByUserId(int $id): array
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM users_roles WHERE user_id=?", "i", $id);
        $users_roles = [];
        foreach ($data as $user_role) {
            $users_roles[] = new UserRole($user_role);
        }
        return $users_roles;
    }
    public function findByRoleId(int $id): array
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM users_roles WHERE role_id=?", "i", $id);
        $users_roles = [];
        foreach ($data as $user_role) {
            $users_roles[] = new UserRole($user_role);
        }
        return $users_roles;
    }

    public function findByUserIdAndRoleId(int $user_id, int $role_id): ?UserRole
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM users_roles WHERE role_id=? AND user_id=?", "ii", $role_id, $user_id);
        return !empty($data) ? new UserRole($data[0]) : null;
    }

    public function insert(UserRole $user_role): UserRole
    {
        DatabaseHelper::preparedQuery(
            "INSERT INTO users_roles (role_id, user_id) VALUES (?, ?)",
            "ii",
            $user_role->getRoleId(),
            $user_role->getUserId()
        );

        $user_role->setId(DatabaseHelper::getLastId());
        return $user_role;
    }

    public function update(UserRole $user_role): UserRole
    {
        DatabaseHelper::preparedQuery(
            "UPDATE users_roles SET role_id=?, user_id=? WHERE id=?",
            "iii",
            $user_role->getRoleId(),
            $user_role->getUserId(),
            $user_role->getId()
        );

        return $user_role;
    }
    public function delete(int $id): bool
    {
        return DatabaseHelper::preparedQuery("DELETE FROM users_roles WHERE id = ?", "i", $id);
    }
}
