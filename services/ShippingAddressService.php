<?php

namespace App\Services;

use App\Models\ShippingAddress;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;
use App\Exceptions\NotFoundException;
use App\Exceptions\DuplicateException;
use App\Repositories\Contracts\ShippingAddressRepositoryInterface;

class ShippingAddressService
{
    private ShippingAddressRepositoryInterface $repository;

    public function __construct(ShippingAddressRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->findAll();
    }

    public function get($id)
    {
        return $this->repository->findById($id);
    }

    public function getByUser($id)
    {
        return $this->repository->findByUserId($id);
    }

    public function delete($id)
    {
        $this->getByUser($id);

        if (!$this->repository->delete($id)) {
            throw new DeleteException("Failed to delete address with ID $id.");
        }
    }

    public function deleteByUser($id)
    {
        $this->getByUser($id);

        if (!$this->repository->deleteByUserId($id)) {
            throw new DeleteException("Failed to delete address with ID $id.");
        }
    }

    public function save($rawAddress)
    {
        if ($this->repository->findByUserId($rawAddress["user_id"])) {
            throw new DuplicateException("The address already exists");
        }

        $address = new ShippingAddress($rawAddress);

        if (!$this->repository->insert($address)) {
            throw new InsertException("Failed to insert address with ID " . $address->getId());
        } else {
            return $address;
        }
    }
    public function update($rawAddress)
    {
        $addressDb = $this->getByUser($rawAddress["user_id"]);

        $address = $this->set($addressDb, $rawAddress);

        if (!$this->repository->update($address)) {
            throw new UpdateException("Failed to update address with ID " . $addressDb->getId());
        }
        return $address;
    }


    private function set($address, $data)
    {
        $allowedFields = ['full_name', 'user_id', 'phone', 'address', 'city', 'province', 'country', 'postal_code'];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));

                if (method_exists($address, $method)) {
                    $address->$method($data[$field]);
                }
            }
        }

        return $address;
    }
}
