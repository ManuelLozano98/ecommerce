<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\User;
use App\Utils\DatabaseHelper;
use App\Utils\PaginationHelper;
use App\Repositories\Contracts\RoleRepositoryInterface;

class RoleRepository implements RoleRepositoryInterface
{

    public function findAll(): array
    {
        $rows = DatabaseHelper::query("SELECT * FROM roles");
        $roles = [];
        foreach ($rows as $role) {
            $roles[] = new Role($role);
        }
        return $roles;
    }
    public function findActive(): array
    {
        $rows = DatabaseHelper::query("SELECT * FROM roles WHERE active = 1");
        $roles = [];
        foreach ($rows as $role) {
            $roles[] = new Role($role);
        }
        return $roles;
    }

    public function findById(int $id): ?Role
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM roles WHERE id = ?", "i", $id);
        return !empty($data) ? new Role($data[0]) : null;
    }
    public function findByName(string $name): ?Role
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM roles WHERE name = ?", "s", $name);
        return !empty($data) ? new Role($data[0]) : null;
    }

    public function findByActive(int $active): array
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM roles WHERE active = ?", "i", $active);
        $roles = [];
        foreach ($data as $role) {
            $roles[] = new Role($role);
        }
        return $roles;
    }
    public function findRolesByUser(User $user): array
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT r.*
        FROM users_roles ur
        INNER JOIN roles r ON r.role_id = ur.role_id
        WHERE ur.user_id = ?", "i", $user->getId());
        $roles = [];
        foreach ($data as $role) {
            $roles[] = new Role($role);
        }
        return $roles;
    }

    public function paginate(array $params): array
    {
        $tableName = "roles";
        $search = $params['search']['value'] ?? '';
        $columns = ['id', 'name', 'description', 'active'];
        $data = PaginationHelper::make($params, $tableName, $columns);


        $filteredRecords = PaginationHelper::getFilteredCount($search, $tableName, $columns);
        $totalRecords = PaginationHelper::getTotalRecords($tableName);

        $paginated = [
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];
        return $paginated;
    }

    public function insert(Role $role): Role
    {
        DatabaseHelper::preparedQuery(
            "INSERT INTO roles (name, description, active) VALUES (?, ?, ?)",
            "ssi",
            $role->getName(),
            $role->getDescription(),
            $role->getActive()
        );

        $role->setId(DatabaseHelper::getLastId());
        return $role;
    }

    public function update(Role $role): Role
    {
        DatabaseHelper::preparedQuery(
            "UPDATE roles SET name=?, description=?, active=? WHERE id=?",
            "ssii",
            $role->getName(),
            $role->getDescription(),
            $role->getActive(),
            $role->getId()
        );

        return $role;
    }
    public function delete(int $id): bool
    {
        return DatabaseHelper::preparedQuery("DELETE FROM roles WHERE id = ?", "i", $id);
    }
}
