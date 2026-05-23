<?php

namespace App\Repositories;

use App\Models\EmailChange;
use App\Utils\DatabaseHelper;
use App\Repositories\Contracts\EmailChangeRepositoryInterface;

class EmailChangeRepository implements EmailChangeRepositoryInterface
{

    public function findAll(): array
    {
        $rows = DatabaseHelper::query("SELECT * FROM email_change_requests");
        $email_changes = [];
        foreach ($rows as $email_change) {
            $email_changes[] = new EmailChange($email_change);
        }
        return $email_changes;
    }

    public function findById(int $id): ?EmailChange
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM email_change_requests WHERE id = ?", "i", $id);
        return !empty($data) ? new EmailChange($data[0]) : null;
    }
    public function findByToken(string $token): ?EmailChange
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM email_change_requests WHERE token = ?", "s", $token);
        return !empty($data) ? new EmailChange($data[0]) : null;
    }

    public function findByActive(int $active): array
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM email_change_requests WHERE used = ?", "i", $active);
        $email_changes = [];
        foreach ($data as $email_change) {
            $email_changes[] = new EmailChange($email_change);
        }
        return $email_changes;
    }

    public function insert(EmailChange $email_change): EmailChange
    {
        DatabaseHelper::preparedQuery(
            "INSERT INTO email_change_requests (user_id, token, old_email, new_email, expires_at, used, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)",
            "issssis",
            $email_change->getUserId(),
            $email_change->getToken(),
            $email_change->getOldEmail(),
            $email_change->getNewEmail(),
            $email_change->getExpiredAt(),
            $email_change->isUsed(),
            $email_change->getCreatedAt()
        );

        $email_change->setId(DatabaseHelper::getLastId());
        return $email_change;
    }

    public function update(EmailChange $email_change): EmailChange
    {
        DatabaseHelper::preparedQuery(
            "UPDATE email_change_requests SET user_id=?, token=?, old_email=?, new_email=?, expires_at=?, used=?, created_at=? WHERE id=?",
            "issssisi",
            $email_change->getUserId(),
            $email_change->getToken(),
            $email_change->getOldEmail(),
            $email_change->getNewEmail(),
            $email_change->getExpiredAt(),
            $email_change->isUsed(),
            $email_change->getCreatedAt(),
            $email_change->getId()
        );

        return $email_change;
    }
    public function delete(int $id): bool
    {
        return DatabaseHelper::preparedQuery("DELETE FROM email_change_requests WHERE id = ?", "i", $id);
    }
}
