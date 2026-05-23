<?php

namespace App\Repositories;

use App\Models\PasswordReset;
use App\Utils\DatabaseHelper;
use App\Repositories\Contracts\PasswordRepositoryInterface;

class PasswordResetRepository implements PasswordRepositoryInterface
{

    public function findAll(): array
    {
        $rows = DatabaseHelper::query("SELECT * FROM password_resets");
        $password_resets = [];
        foreach ($rows as $password_reset) {
            $password_resets[] = new PasswordReset($password_reset);
        }
        return $password_resets;
    }

    public function findById(int $id): ?PasswordReset
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM password_resets WHERE id = ?", "i", $id);
        return !empty($data) ? new PasswordReset($data[0]) : null;
    }
    public function findByToken(string $token): ?PasswordReset
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM password_resets WHERE token = ?", "s", $token);
        return !empty($data) ? new PasswordReset($data[0]) : null;
    }

    public function findByActive(int $active): array
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM password_resets WHERE used = ?", "i", $active);
        $password_resets = [];
        foreach ($data as $password_reset) {
            $password_resets[] = new PasswordReset($password_reset);
        }
        return $password_resets;
    }

    public function findByUser(int $user): ?PasswordReset
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM password_resets WHERE user_id = ?", "i", $user);
        return !empty($data) ? new PasswordReset($data[0]) : null;
    }

    public function insert(PasswordReset $password_reset): PasswordReset
    {
        DatabaseHelper::preparedQuery(
            "INSERT INTO password_resets (user_id, token, expires_at, used, created_at) VALUES (?, ?, ?, ?, ?)",
            "issis",
            $password_reset->getUserId(),
            $password_reset->getToken(),
            $password_reset->getExpiredAt(),
            $password_reset->isUsed(),
            $password_reset->getCreatedAt()
        );

        $password_reset->setId(DatabaseHelper::getLastId());
        return $password_reset;
    }

    public function update(PasswordReset $password_reset): PasswordReset
    {
        DatabaseHelper::preparedQuery(
            "UPDATE password_resets SET user_id=?, token=?, expires_at=?, used=?, created_at=? WHERE id=?",
            "issisi",
            $password_reset->getUserId(),
            $password_reset->getToken(),
            $password_reset->getExpiredAt(),
            $password_reset->isUsed(),
            $password_reset->getCreatedAt(),
            $password_reset->getId()
        );

        return $password_reset;
    }
    public function delete(int $id): bool
    {
        return DatabaseHelper::preparedQuery("DELETE FROM password_resets WHERE id = ?", "i", $id);
    }
}
