<?php

namespace App\Models;

use JsonSerializable;

class ShippingAddress implements JsonSerializable
{

    private int $id;
    private int $user_id;
    private string $full_name;
    private string $phone;
    private string $address;
    private string $city;
    private string $province;
    private string $country;
    private string $postal_code;

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }


    function __construct($data = [])
    {
        $this->id = $data['id'] ?? 0;
        $this->user_id = $data['user_id'] ?? 1;
        $this->full_name = $data['full_name'] ?? "";
        $this->phone = $data['phone'] ?? "";
        $this->address = $data['address'] ?? "";
        $this->city = $data['city'] ?? "";
        $this->province = $data['province'] ?? "";
        $this->country = $data['country'] ?? "";
        $this->postal_code = $data['postal_code'] ?? "";
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'province' => $this->province,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
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

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    public function getFullName()
    {
        return $this->full_name;
    }

    public function setFullName($name)
    {
        $this->full_name = $name;

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


    public function getCity()
    {
        return $this->city;
    }


    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }


    public function getProvince()
    {
        return $this->province;
    }


    public function setProvince($province)
    {
        $this->province = $province;

        return $this;
    }

    public function getCountry()
    {
        return $this->country;
    }


    public function setCountry($country)
    {
        $this->country = $country;

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

    public function getPostalCode()
    {
        return $this->postal_code;
    }


    public function setPostalCode($postal_code)
    {
        $this->postal_code = $postal_code;

        return $this;
    }
}
