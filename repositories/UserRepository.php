<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\User;
use App\Utils\DatabaseHelper;
use App\Utils\PaginationHelper;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{

    public function findAll(): array
    {
        $rows = DatabaseHelper::query("SELECT * FROM users");
        $users = [];
        foreach ($rows as $user) {
            $users[] = new User($user);
        }
        return $users;
    }

    public function findById(int $id): ?User
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM users WHERE id = ?", "i", $id);
        return !empty($data) ? new User($data[0]) : null;
    }

    public function findByUsername(string $username): ?User
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM users WHERE username = ?", "s", $username);
        return !empty($data) ? new User($data[0]) : null;
    }
    public function findByEmail(string $email): ?User
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM users WHERE email = ?", "s", $email);
        return !empty($data) ? new User($data[0]) : null;
    }
    public function findByPhone(string $phone): ?User
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM users WHERE phone = ?", "s", $phone);
        return !empty($data) ? new User($data[0]) : null;
    }
    public function findByToken(string $token): ?User
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM users WHERE verification_token = ?", "s", $token);
        return !empty($data) ? new User($data[0]) : null;
    }

    public function findByName(string $name): array
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM users WHERE name = ?", "s", $name);
        $users = [];
        foreach ($data as $user) {
            $users[] = new User($user);
        }
        return $users;
    }

    public function findByUserId(int $id): array
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM users WHERE user_id = ?", "i", $id);
        $users = [];
        foreach ($data as $row) {
            $users[] = new User($row);
        }

        return $users;
    }

    public function findByProductId(int $id): array
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM users WHERE id = (SELECT user_id FROM user_items WHERE product_id = ?);", "i", $id);
        $users = [];
        foreach ($data as $row) {
            $users[] = new User($row);
        }

        return $users;
    }

    public function findUsersByRolename(string $role_name): array
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM users WHERE user_id IN (
SELECT u_r.user_id FROM users_roles u_r WHERE u_r.role_id = (
    SELECT role_id FROM roles WHERE name = '?'))", "s", $role_name);
        $users = [];
        foreach ($data as $row) {
            $users[] = new User($row);
        }

        return $users;
    }
    public function findUsersByRole(Role $role): array
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM users WHERE user_id IN (
SELECT u_r.user_id FROM users_roles u_r WHERE u_r.role_id = ?)", "i", $role->getId());
        $users = [];
        foreach ($data as $row) {
            $users[] = new User($row);
        }

        return $users;
    }

    public function countAll(): int
    {
        return (int) DatabaseHelper::query(
            "SELECT COUNT(*) AS total FROM users"
        )[0]['total'];
    }

    public function countUsersRegisteredLast7Days(): array
    {
        $data = DatabaseHelper::query("SELECT COUNT(*) AS records
            FROM users
            WHERE registration_date >= NOW() - INTERVAL 7 DAY");
        $users = [];
        foreach ($data as $row) {
            $users[] = new User($row);
        }

        return $users;
    }

    public function activateAccount(User $user): bool
    {
        return DatabaseHelper::preparedQuery("UPDATE users SET active = ?, verification_token = ? WHERE id = ?", "isi", 1, NULL, $user->getId());
    }

    public function hasRole(User $user, string $role_name): bool
    {
        return DatabaseHelper::getDataPreparedQuery("SELECT id FROM users_roles WHERE user_id = ? AND role_id = (SELECT role_id FROM roles WHERE name = ?)", "is", $user->getId(), $role_name)[0];
    }
    public function paginate(array $params): array
    {
        $tableName = "users";
        $search = $params['search']['value'] ?? '';
        $columns = ['id', 'name', 'email', 'username', 'phone', 'image', 'address', 'document', 'document_type_id', 'verification_token', 'token_expires_at', 'registration_date', 'active']; //The columns must be in the same order as front end user table
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

    public function paginateDetailed(array $params): array
    {
        $selectFields = [
            'u.id',
            'u.name',
            'u.email',
            'u.username',
            'u.phone',
            'u.image',
            'u.address',
            'u.document',
            'u.document_type_id',
            'u.verification_token',
            'u.token_expires_at',
            'd.name AS document_name',
            'u.registration_date',
            'u.active'
        ];

        $columns = [
            'u.id',
            'u.name',
            'u.email',
            'u.username',
            'u.phone',
            'u.image',
            'u.address',
            'u.document',
            'd.name',
            'u.verification_token',
            'u.token_expires_at',
            'u.registration_date',
            'u.active'
        ];

        $fromClause = 'users u JOIN document_types d ON u.document_type_id = d.id';
        $search = $params['search']['value'] ?? '';

        $data = PaginationHelper::makeCustom($params, $fromClause, $columns, $selectFields);
        $totalRecords = PaginationHelper::getTotalRecordsCustom($fromClause);
        $filteredRecords = PaginationHelper::getFilteredCustomCount($search, $fromClause, $columns);


        $paginated = [
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];
        return $paginated;
    }

    public function insert(User $user): User
    {
        DatabaseHelper::preparedQuery(
            "INSERT INTO users (name, email, password, username, phone, image, address, document, document_type_id, active, verification_token, token_expires_at,registration_date) VALUES (?, ?, ?,?,?,?,?,?,?,?,?,?,?)",
            "ssssssssiisss",
            $user->getName(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getUsername(),
            $user->getPhone(),
            $user->getImage(),
            $user->getAddress(),
            $user->getDocument(),
            $user->getDocumentType(),
            $user->getActive(),
            $user->getToken(),
            $user->getTokenExpiredAt(),
            $user->getRegistrationDate()
        );

        $user->setId(DatabaseHelper::getLastId());
        return $user;
    }

    public function update(User $user): User
    {
        DatabaseHelper::preparedQuery(
            "UPDATE users SET name=?, email=?, password=?, username=?, phone=?, image=?, address=?, document=?, document_type_id=?, active=?, verification_token=?, token_expires_at=?,registration_date=? WHERE id=?",
            "ssssssssiisssi",
            $user->getName(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getUsername(),
            $user->getPhone(),
            $user->getImage(),
            $user->getAddress(),
            $user->getDocument(),
            $user->getDocumentType(),
            $user->getActive(),
            $user->getToken(),
            $user->getTokenExpiredAt(),
            $user->getRegistrationDate(),
            $user->getId()
        );

        return $user;
    }

    public function updatePassword(int $userId, string $passwordHash): User
    {
        DatabaseHelper::preparedQuery(
            "UPDATE users SET password=? WHERE id=?",
            "si",
            $passwordHash,
            $userId
        );
        return $this->findById($userId);
    }

    public function updateEmail(User $user, string $email): User
    {
        DatabaseHelper::preparedQuery(
            "UPDATE users SET email=? WHERE id=?",
            "si",
            $email,
            $user->getId()
        );
        return $user;
    }

    public function updateProfile(User $user, array $data): User
    {
        DatabaseHelper::preparedQuery(
            "UPDATE users SET name=?, address=?, phone=? WHERE id=?",
            "sssi",
            $data['name'],
            $data['address'],
            $data['phone'],
            $user->getId()
        );
        return $user;
    }
    public function updateToken(User $user, string $token, string $expires_at): User
    {
        DatabaseHelper::preparedQuery(
            "UPDATE users SET verification_token=?, token_expires_at=? WHERE id=?",
            "ssi",
            $token,
            $expires_at,
            $user->getId()
        );
        return $user;
    }
    public function delete(int $id): bool
    {
        return DatabaseHelper::preparedQuery("DELETE FROM users WHERE id = ?", "i", $id);
    }
}
