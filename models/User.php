<?php

namespace App\Models;

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
        $this->active = (bool) ($data['active'] ?? false);
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
