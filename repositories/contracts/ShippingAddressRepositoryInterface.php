<?php

namespace App\Repositories\Contracts;

use App\Models\ShippingAddress;

interface ShippingAddressRepositoryInterface
{
    public function findAll(): array;

    public function findById(int $id): ?ShippingAddress;

    public function findByUserId(int $userId): ?ShippingAddress;

    public function insert(ShippingAddress $address): ShippingAddress;

    public function update(ShippingAddress $address): ShippingAddress;

    public function delete(int $id): bool;

    public function deleteByUserId(int $id): bool;
}
