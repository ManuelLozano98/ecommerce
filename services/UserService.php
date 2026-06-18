<?php

namespace App\Services;

use App\Dtos\UserPublicDTO;
use App\Services\DocumentTypeService;
use App\Models\User;
use DateTime;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\NotFoundException;
use App\Exceptions\DuplicateException;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Exceptions\InvalidCredentialsException;

class UserService
{
    private UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->findAll();
    }

    public function getUser($id)
    {
        $user = $this->repository->findById($id);
        if (!$user) {
            throw new NotFoundException("The user was not found or not exists");
        }
        return $user;
    }

    public function getDocumentTypes(DocumentTypeService $documentTypeService)
    {
        return $documentTypeService->getAll();
    }

    public function getUserCountLast7Days()
    {
        return $this->repository->countUsersRegisteredLast7Days();
    }

    public function getUserByEmail($email)
    {
        return $this->repository->findByEmail($email);
    }

    public function getUserByToken($token)
    {
        return $this->repository->findByToken($token);
    }


    public function logIn($loginData)
    {
        $login = $loginData["login"] ?? "";
        $password = $loginData["password"] ?? "";
        $user = $this->isEmail($login)
            ? $this->repository->findByEmail($login)
            : $this->repository->findByUsername($login);

        if (!$user) {
            throw new InvalidCredentialsException();
        }

        if (!$user->getActive()) {
            throw new \Exception("Check the email and activates your account");
        }

        if (!$this->checkPassword($password, $user->getPassword())) {
            throw new InvalidCredentialsException();
        }
        session_regenerate_id(true);
        $_SESSION["user"] = ['data' => new UserPublicDTO($user)];

        return true;
    }

    public function hasRole(User $user, $roleName)
    {
        return $this->repository->hasRole($user, $roleName);
    }

    public function delete($id)
    {
        $this->getUser($id);

        if (!$this->repository->delete($id)) {
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
        $user = $this->repository->findByToken($token);
        if (!$user) {
            throw new NotFoundException("The user was not found or not exists");
        }
        $date = (new DateTime('now'))->format('Y-m-d H:i:s');
        if (($user->getActive() === false || $user->getActive() === 0) && $user->getTokenExpiredAt() > $date) {
            if (!$this->repository->activateAccount($user)) {
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

    public function paginate($params)
    {
        return $this->repository->paginate($params);
    }

    public function paginateDetailed($params)
    {
        return $this->repository->paginateDetailed($params);
    }

    public function save($rawUser)
    {
        if ($this->repository->findByUsername($rawUser["username"]) || $this->repository->findByEmail($rawUser["email"])) {
            throw new DuplicateException("The username or email already exists");
        }

        $user = new User($rawUser);
        $hash = password_hash($user->getPassword(), PASSWORD_ARGON2ID);
        $user->setPassword($hash);
        $user = $this->generateToken($user);

        try {
            return $this->repository->insert($user);
        } catch (\Throwable $e) {
            throw new InsertException("Failed to insert user");
        }
    }
    public function update($rawUser)
    {
        $userDb = $this->getUser($rawUser["id"]);

        $userNameFound = $this->repository->findByUsername($rawUser["username"]);

        if ($userNameFound && $userNameFound->getId() !== $userDb->getId()) {
            throw new DuplicateException("The username already exists");
        }

        $emailFound = $this->repository->findByEmail($rawUser["email"]);
        if ($emailFound && $emailFound->getId() !== $userDb->getId()) {
            throw new DuplicateException("The email already exists");
        }
        $user = $this->set($userDb, $rawUser);

        try {
            return $this->repository->update($user);
        } catch (\Throwable $e) {
            throw new UpdateException("Failed to update user");
        }
    }

    public function updateEmail(User $user, string $email)
    {
        if (!$user || !$this->repository->findById($user->getId())) {
            throw new NotFoundException("The user was not found or not exists");
        }
        try {
            return $this->repository->updateEmail($user, $email);
        } catch (\Throwable $e) {
            throw new UpdateException("Failed to update user");
        }
    }

    public function updatePassword(int $userId, string $password)
    {
        $passwordHash = password_hash($password, PASSWORD_ARGON2ID);
        try {
            $this->repository->updatePassword($userId, $passwordHash);
            return true;
        } catch (\Throwable $e) {
            throw new UpdateException("Failed to update user");
        }
    }

    public function updateProfile(User $user, array $data)
    {
        if (!$user || !$this->repository->findById($user->getId())) {
            throw new NotFoundException("The user was not found or not exists");
        }
        if (!isset($data['name']) || $data['name'] === '') {
            $data['name'] = $user->getName();
        }
        if (!isset($data['phone']) || $data['phone'] === '') {
            $data['phone'] = $user->getPhone();
        }
        if (!isset($data['address']) || $data['address'] === '') {
            $data['address'] = $user->getAddress();
        }
        try {
            return $this->repository->updateProfile($user, $data);
        } catch (\Throwable $e) {
            throw new UpdateException("Failed to update user");
        }
    }

    public function updateToken(User $user)
    {
        if (!$user || !$this->repository->findById($user->getId())) {
            throw new NotFoundException("The user was not found or not exists");
        }
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 day'));

        try {
            return $this->repository->updateToken($user, $token, $expiresAt);
        } catch (\Throwable $e) {
            throw new UpdateException("Failed to update user");
        }
    }


    private function set($user, $data)
    {
        $allowedFields = ['name', 'email', 'username', 'phone', 'address', 'document', 'document_type', 'token', 'token_expired_at', 'registration_date', 'active'];
        if (isset($data['email']) && $data["email"] === "") {
            $allowedFields = array_diff($allowedFields, ["email"]);
        }
        if (!empty($data["password"])) {
            $user->setPassword(password_hash($data['password'], PASSWORD_ARGON2ID));
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
        $isValid = $this->validateImage($image);
        if ($isValid) {
            $user = $this->getUser($userId);
            $upload  = $this->uploadImage($image);
            $user->setImage($upload);
            $this->repository->update($user);
            return $user->getImage();
        }
    }

    private function validateImage($image)
    {
        if ($image->getError() !== UPLOAD_ERR_OK) {
            return false;
        }

        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($image->getSize() > $maxSize) {
            return false;
        }
        return true;
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
