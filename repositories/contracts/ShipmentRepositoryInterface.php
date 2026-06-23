<?php

namespace App\Repositories\Contracts;

use App\Models\Shipment;

interface ShipmentRepositoryInterface
{
    public function findAll(): array;

    public function findById(int $id): ?Shipment;

    public function findByTrackingNumber(string $number): ?Shipment;

    public function findBySaleId(int $saleId): ?Shipment;

    public function findByStripeId(string $id): ?Shipment;

    public function insert(Shipment $shipment): Shipment;

    public function update(Shipment $shipment): Shipment;

    public function delete(int $id): bool;
}
