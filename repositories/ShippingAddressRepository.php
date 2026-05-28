<?php

namespace App\Repositories;

use App\Models\ShippingAddress;
use App\Utils\DatabaseHelper;
use App\Repositories\Contracts\ShippingAddressRepositoryInterface;

class ShippingAddressRepository implements ShippingAddressRepositoryInterface
{

    public function findAll(): array
    {
        $rows = DatabaseHelper::query("SELECT * FROM addresses");
        $addresses = [];
        foreach ($rows as $address) {
            $addresses[] = new ShippingAddress($address);
        }
        return $addresses;
    }

    public function findById(int $id): ?ShippingAddress
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM addresses WHERE id = ?", "i", $id);
        return !empty($data) ? new ShippingAddress($data[0]) : null;
    }
    public function findByUserId(int $userId): ?ShippingAddress
    {
        $data = DatabaseHelper::getDataPreparedQuery("SELECT * FROM addresses WHERE user_id = ?", "i", $userId);
        return !empty($data) ? new ShippingAddress($data[0]) : null;
    }

    public function insert(ShippingAddress $address): ShippingAddress
    {
        DatabaseHelper::preparedQuery(
            "INSERT INTO addresses (user_id, full_name, phone, address, city, province, country, postal_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            "issssssi",
            $address->getUserId(),
            $address->getFullName(),
            $address->getPhone(),
            $address->getAddress(),
            $address->getCity(),
            $address->getProvince(),
            $address->getCountry(),
            $address->getPostalCode(),
        );

        $address->setId(DatabaseHelper::getLastId());
        return $address;
    }

    public function update(ShippingAddress $address): ShippingAddress
    {
        DatabaseHelper::preparedQuery(
            "UPDATE addresses SET user_id=?, full_name=?, phone=?, address=?, city=?, province=?, country=?, postal_code=? WHERE id=?",
            "issssssii",
            $address->getUserId(),
            $address->getFullName(),
            $address->getPhone(),
            $address->getAddress(),
            $address->getCity(),
            $address->getProvince(),
            $address->getCountry(),
            $address->getPostalCode(),
            $address->getId()
        );

        return $address;
    }
    public function delete(int $id): bool
    {
        return DatabaseHelper::preparedQuery("DELETE FROM addresses WHERE id = ?", "i", $id);
    }

    public function deleteByUserId(int $id): bool
    {
        return DatabaseHelper::preparedQuery("DELETE FROM addresses WHERE user_id = ?", "i", $id);
    }
}
