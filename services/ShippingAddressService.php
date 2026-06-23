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
        $address = $this->repository->findById($id);

        if (!$address) {
            throw new NotFoundException("Address not found");
        }

        return $address;
    }

    public function getByUser($id)
    {
        return $this->repository->findByUserId($id);
    }

    public function delete($id)
    {
        $address = $this->repository->findById($id);

        if (!$address) {
            throw new NotFoundException("Address not found");
        }

        if (!$this->repository->delete($id)) {
            throw new DeleteException("Failed to delete address with ID $id");
        }
    }

    public function deleteByUser($userId)
    {
        $addresses = $this->repository->findByUserId($userId);

        if (empty($addresses)) {
            throw new NotFoundException("No addresses found for user");
        }

        if (!$this->repository->deleteByUserId($userId)) {
            throw new DeleteException("Failed to delete addresses for user $userId");
        }
    }

    public function save($rawAddress)
    {
        if ($this->repository->findByUserId($rawAddress["user_id"])) {
            throw new DuplicateException("The address already exists");
        }
        $address = new ShippingAddress($rawAddress);

        try {
            return $this->repository->insert($address);
        } catch (\Throwable $e) {
            throw new InsertException("Failed to insert address");
        }
    }
    public function update($rawAddress)
    {
        $addressDb = $this->repository->findById($rawAddress["id"]);

        if (!$addressDb) {
            throw new NotFoundException("Address not found");
        }

        $address = $this->set($addressDb, $rawAddress);

        try {
            return $this->repository->update($address);
        } catch (\Throwable $e) {
            throw new UpdateException("Failed to update address");
        }
    }

    public function getCountryFromAddressOSM($address, $city = '', $state = '')
    {
        $query = urlencode("$address, $city, $state");


        // Open Street Map
        //Set addressdetails=1 to get country
        $url = "https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&q={$query}";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: Ecommerce/1.0'
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return null;
        }

        $data = json_decode($response, true);


        if (!empty($data[0]['address']['country'])) {
            return $data[0]['address']['country'];
        }

        return null;
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
