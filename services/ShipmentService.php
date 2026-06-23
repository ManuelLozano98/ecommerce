<?php

namespace App\Services;

use App\Models\Shipment;
use App\Repositories\Contracts\ShipmentRepositoryInterface;
use App\Exceptions\NotFoundException;
use App\Exceptions\InsertException;
use App\Exceptions\UpdateException;
use App\Exceptions\DeleteException;

class ShipmentService
{
    private ShipmentRepositoryInterface $repository;

    public function __construct(ShipmentRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->findAll();
    }

    public function getById($id)
    {
        $shipment = $this->repository->findById($id);

        if (!$shipment) {
            throw new NotFoundException("Shipment not found");
        }

        return $shipment;
    }

    public function getByTrackingNumber($tracking)
    {
        $shipment = $this->repository->findByTrackingNumber($tracking);

        if (!$shipment) {
            throw new NotFoundException("Shipment not found");
        }

        return $shipment;
    }

    public function getBySaleId($saleId)
    {
        $shipment = $this->repository->findBySaleId($saleId);

        if (!$shipment) {
            throw new NotFoundException("Shipment not found for sale");
        }

        return $shipment;
    }

    public function getByStripeId($id)
    {
        $shipment = $this->repository->findByStripeId($id);
        return $shipment;
    }

    public function save($raw)
    {
        $shipment = new Shipment($raw);

        try {
            return $this->repository->insert($shipment);
        } catch (\Throwable $e) {
            throw new InsertException("Failed to create shipment");
        }
    }

    public function update($raw)
    {
        $shipment = $this->getById($raw['id']);

        $this->set($shipment, $raw);

        try {
            return $this->repository->update($shipment);
        } catch (\Throwable $e) {
            throw new UpdateException("Failed to update shipment");
        }
    }

    public function delete($id)
    {
        $this->getById($id);

        if (!$this->repository->delete($id)) {
            throw new DeleteException("Failed to delete shipment");
        }
    }

    private function set($shipment, $raw)
    {
        $allowed = [
            'sale_id',
            'shippo_shipment_id',
            'shippo_transaction_id',
            'stripe_session_id',
            'tracking_number',
            'carrier',
            'status',
            'label_url'
        ];

        foreach ($allowed as $field) {
            if (isset($raw[$field])) {
                $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));

                if (method_exists($shipment, $method)) {
                    $shipment->$method($raw[$field]);
                }
            }
        }

        return $shipment;
    }
}
