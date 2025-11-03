<?php

namespace App\Services;

use App\Services\MailService;
use App\Services\DocumentTypeService;
use App\Models\User;
use DateTime;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\NotFoundException;
use App\Exceptions\DuplicateException;


class UserService
{

    public function getUsers()
    {
        return User::getAll();
    }

    public function getUser($id)
    {
        $user = User::findById($id);
        if (!$user) {
            throw new NotFoundException("The user was not found or not exists");
        }
        return $user;
    }
    public function getUsersWithDocumentTypeName()
    {
        return User::getAllWithDocumentTypeName();
    }

    public function getDocumentTypes(DocumentTypeService $documentTypeService)
    {
        return $documentTypeService->getDocument_Types();
    }

    public function getUsernames()
    {
        $user = User::getUsernames();
        if (!$user) {
            throw new NotFoundException("The user was not found or not exists");
        }
        $usernames = array_map(function ($user) {
            return [
                "id" => $user->getId(),
                "username" => $user->getUsername()
            ];
        }, $user);
        return $usernames;
    }

    public function getUserCountLast7Days()
    {
        return User::getUserCountLast7Days();
    }


    public function logIn($loginData)
    {
        $login = $loginData["login"];
        $password = $loginData["password"];

        $data = $this->isEmail($login)
            ? User::findByEmail($login)
            : User::findByUsername($login);
        if (!$data) {
            throw new NotFoundException("The user was not found or not exists");
        }
        if ($data->getActive() === 1 && $this->checkPassword($password, $data->getPassword())) {
            session_start();
            $_SESSION["user"] = $data;
            session_regenerate_id(true);
        }
    }

    public function deleteUser($id)
    {
        if (!User::findById($id)) {
            throw new NotFoundException("The user was not found or not exists");
        }

        if (!User::delete($id)) {
            throw new DeleteException("Failed to delete user with ID $id.");
        }
    }

    private function checkPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    private function isEmail($param)
    {
        return filter_var($param, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function activateAccount($token)
    {
        $user = User::findByToken($token);
        if (!$user) {
            throw new NotFoundException("The user was not found or not exists");
        }
        $date = (new DateTime('now'))->format('Y-m-d H:i:s');
        if ($user->getActive() === 0 && $user->getTokenExpiredAt() > $date) {
            if (!User::activateAccount($user)) {
                throw new UpdateException("Failed to activate user with ID " . $user->getId());
            }
        }
    }

    private function generateToken(User $user)
    {
        $user->setToken(bin2hex(random_bytes(32)));
        $user->setTokenExpiredAt((new DateTime('+1 day'))->format('Y-m-d H:i:s'));
        return $user;
    }

    public function saveUser($method, $rawUser)
    {
        if ($method === "POST") {
            if (User::findByUsername($rawUser["username"]) || User::findByEmail($rawUser["email"])) {
                throw new DuplicateException("The username or email already exists");
            }

            $user = new User($rawUser);
            $hash = password_hash($user->getPassword(), PASSWORD_BCRYPT);
            $user->setPassword($hash);
            $user = $this->generateToken($user);

            if (!User::insert($user)) {
                throw new InsertException("Failed to insert user with ID " . $user->getId());
            } else {
                return $user;
            }
        } else {
            $userDb = User::findById($rawUser["id"]);

            if (!$userDb) {
                throw new NotFoundException("The user was not found or not exists");
            }
            $userNameFound = User::findByUsername($rawUser["username"]);

            if ($userNameFound && $userNameFound->getId() !== $userDb->getId()) {
                throw new DuplicateException("The username already exists");
            }
            $emailFound = User::findByEmail($rawUser["email"]);
            if ($emailFound && $emailFound->getId() !== $userDb->getId()) {
                throw new DuplicateException("The email already exists");
            }
            $user = $this->set($userDb, $rawUser);

            if (!User::edit($user)) {
                throw new UpdateException("Failed to update user with ID " . $userDb->getId());
            } else {
                return $user;
            }
        }
    }

    private function set($user, $data)
    {
        $allowedFields = ['name', 'email', 'username', 'phone', 'address', 'document', 'document_type', 'token', 'token_expired_at', 'registration_date', 'active'];
        if ($data["email"] === "") {
            $allowedFields = array_diff($allowedFields, ["email"]);
        }
        if ($data["password"] !== "") {
            $hash = password_hash($user->getPassword(), PASSWORD_ARGON2ID);
            $user->setPassword($hash);
        }

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));

                if (method_exists($user, $method)) {
                    $user->$method($data[$field]);
                }
            }
        }

        return $user;
    }

    public function saveImage($image, $userId)
    {
        $user = User::findById($userId);
        if (!$user) {
            throw new NotFoundException("The user was not found or not exists");
        }

        $upload  = $this->uploadImage($image);
        $user->setImage($upload);
        User::edit($user);
        return $user->getImage();
    }

    private function uploadImage($image)
    {
        $extension = pathinfo($image->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);
        $uploadPath = __DIR__ . '/../uploads/images/users/' . $filename;
        $relativeUploadPath = "users/" . $filename;
        $image->moveTo($uploadPath, $filename);
        return $relativeUploadPath;
    }
}
