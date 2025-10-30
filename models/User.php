<?php

namespace App\Models;

use App\Utils\DatabaseHelper;
use JsonSerializable;
use DateTime;

class User implements JsonSerializable
{

    private int $id;
    private string $name;
    private string $username;
    private string $email;
    private string $password;
    private string $phone;
    private string $image;
    private string $address;
    private string $document;
    private int $document_type_id;
    private string $token;
    private string $tokenExpiredAt;
    private bool $active;
    private string $registration_date;

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }


    function __construct($data = [])
    {
        $this->id = $data['id'] ?? 0;
        $this->name = $data['name'] ?? "";
        $this->username = $data['username'] ?? "";
        $this->email = $data['email'] ?? "";
        $this->password = $data['password'] ?? "";
        $this->phone = $data['phone'] ?? "";
        $this->address = $data['address'] ?? "";
        $this->image = $data['image'] ?? "";
        $this->document = $data['document'] ?? "";
        $this->document_type_id = $data['document_type_id'] ?? 1;
        $this->active = $data['active'] ?? 0;
        $this->token = $data['verification_token'] ?? "";
        $this->tokenExpiredAt = $data['token_expires_at'] ?? "";
        $this->registration_date = empty($data['registration_date']) ? (new Datetime("now"))->format('Y-m-d H:i:s') : $data['registration_date'];
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'phone' => $this->phone,
            'image' => $this->image,
            'address' => $this->address,
            'document' => $this->document,
            'document_type_id' => $this->document_type_id,
            'active' => $this->active,
            'verification_token' => $this->token,
            'tokenExpiredAt' => $this->tokenExpiredAt,
            'registration_date' => $this->registration_date,
        ];
    }


    public static function insert(User $user)
    {
        $sql = "INSERT INTO users (name, email, password, username, phone, image, address, document, document_type_id, active, verification_token, token_expires_at,registration_date) VALUES (?, ?, ?,?,?,?,?,?,?,?,?,?,?)";
        $success = DatabaseHelper::preparedQuery(
            $sql,
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

        if ($success) {
            $user->setId(DatabaseHelper::getLastId());
            return $user;
        }

        return false;
    }

    public static function edit(User $user)
    {

        $sql = "UPDATE users SET name=?, email=?, password=?, username=?, phone=?, image=?, address=?, document=?, document_type_id=?, active=?, verification_token=?, token_expires_at=?,registration_date=? WHERE id=?";
        $success = DatabaseHelper::preparedQuery(
            $sql,
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
        return $success ? $user : false;
    }
    public static function delete($id)
    {
        $sql = "DELETE FROM users WHERE id = ?";
        return DatabaseHelper::preparedQuery($sql, "i", $id);
    }

    public static function findById($id)
    {
        $sql = "SELECT * FROM users WHERE id=?";
        $data = DatabaseHelper::getDataPreparedQuery($sql, "i", $id);
        return !empty($data) ? new User($data[0]) : false;
    }
    public static function findByUsername($username)
    {
        $sql = "SELECT * FROM users WHERE username = ?";
        $data = DatabaseHelper::getDataPreparedQuery($sql, "s", $username);
        return !empty($data) ? new User($data[0]) : false;
    }
    public static function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email=?";
        $data = DatabaseHelper::getDataPreparedQuery($sql, "s", $email);
        return !empty($data) ? new User($data[0]) : false;
    }
    public static function findByPhone($phone)
    {
        $sql = "SELECT * FROM users WHERE phone=?";
        $data = DatabaseHelper::getDataPreparedQuery($sql, "s", $phone);
        return !empty($data) ? new User($data[0]) : false;
    }

    public static function findByToken($token)
    {
        $sql = "SELECT * FROM users WHERE verification_token=?";
        $data = DatabaseHelper::getDataPreparedQuery($sql, "s", $token);
        return !empty($data) ? new User($data[0]) : false;
    }
    public static function findByDocumentType($documentType)
    {
        $sql = "SELECT * FROM users WHERE verification_token=?";
        $data = DatabaseHelper::getDataPreparedQuery($sql, "s", $documentType);
        return !empty($data) ? new User($data[0]) : false;
    }

    public static function findByName($name)
    {
        $sql = "SELECT * FROM users WHERE name = ?";
        $data = DatabaseHelper::getDataPreparedQuery($sql, "s", $name);
        $users = [];
        foreach ($data as $user) {
            $users[] = new User($user);
        }
        return $users;
    }

    public static function getAll()
    {
        $sql = "SELECT * FROM users";
        $query = DatabaseHelper::query($sql);
        $users = [];
        foreach ($query as $user) {
            $users[] = new User($user);
        }
        return $users;
    }
    public static function getAllWithDocumentTypeName()
    {
        $sql = "SELECT u.*, d.name AS category_name FROM users u, document_types d WHERE u.document_type_id = d.id";
        $query = DatabaseHelper::query($sql);
        $users = [];
        foreach ($query as $row) {
            $users[] = new User($row);
        }
        return $users;
    }
    public static function getCountUsers()
    {
        $sql = "SELECT COUNT(*) AS records FROM users";
        return DatabaseHelper::query($sql);
    }

    public static function getUserCountLast7Days()
    {
        $sql = "SELECT COUNT(*) AS 'records'
        FROM users
        WHERE DATE(registration_date) BETWEEN DATE(NOW() - INTERVAL 6 DAY) AND CURDATE()";
        $data = DatabaseHelper::query($sql);
        return $data;
    }

    public static function activateAccount(User $user)
    {
        $sql = "UPDATE users SET active = ?, verification_token = ? WHERE id = ?";
        return DatabaseHelper::preparedQuery($sql, "isi", 1, NULL, $user->getId());
    }

    public static function getUsernames()
    {
        $sql = "SELECT id, username FROM users";
        $query = DatabaseHelper::query($sql);
        $users = [];
        foreach ($query as $user) {
            $users[] = new User($user);
        }
        return $users;
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

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }


    public function getEmail()
    {
        return $this->email;
    }


    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }


    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }


    public function getPhone()
    {
        return $this->phone;
    }


    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }


    public function getImage()
    {
        return $this->image;
    }


    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }


    public function getActive()
    {
        return $this->active;
    }


    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    public function getDocument()
    {
        return $this->document;
    }


    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }


    public function getAddress()
    {
        return $this->address;
    }


    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    public function getDocumentType()
    {
        return $this->document_type_id;
    }


    public function setDocumentType($document_type_id)
    {
        $this->document_type_id = $document_type_id;

        return $this;
    }


    public function getUsername()
    {
        return $this->username;
    }


    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }


    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    public function getTokenExpiredAt()
    {
        return $this->tokenExpiredAt;
    }

    public function setTokenExpiredAt($tokenExpiredAt)
    {
        $this->tokenExpiredAt = $tokenExpiredAt;

        return $this;
    }

    public function getRegistrationDate()
    {
        return $this->registration_date;
    }

    public function setRegistrationDate($registration_date)
    {
        $this->registration_date = $registration_date;

        return $this;
    }
}
