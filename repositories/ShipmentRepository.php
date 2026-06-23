<?php

namespace App\Repositories;

use App\Models\Shipment;
use App\Utils\DatabaseHelper;
use App\Repositories\Contracts\ShipmentRepositoryInterface;

class ShipmentRepository implements ShipmentRepositoryInterface
{
    public function findAll(): array
    {
        $rows = DatabaseHelper::query("SELECT * FROM shipments");

        return array_map(fn($row) => new Shipment($row), $rows);
    }

     public function findById(int $id): ?Shipment
    {
        $data = DatabaseHelper::getDataPreparedQuery(
            "SELECT * FROM shipments WHERE id = ?",
            "i",
            $id
        );

        return !empty($data) ? new Shipment($data[0]) : null;
    }

    public function findByTrackingNumber(string $number): ?Shipment
    {
        $data = DatabaseHelper::getDataPreparedQuery(
            "SELECT * FROM shipments WHERE tracking_number = ?",
            "s",
            $number
        );

        return !empty($data) ? new Shipment($data[0]) : null;
    }

    public function findBySaleId(int $saleId): ?Shipment
    {
        $data = DatabaseHelper::getDataPreparedQuery(
            "SELECT * FROM shipments WHERE sale_id = ?",
            "i",
            $saleId
        );

        return !empty($data) ? new Shipment($data[0]) : null;
    }

    public function findByStripeId(string $id): ?Shipment
    {
        $data = DatabaseHelper::getDataPreparedQuery(
            "SELECT * FROM shipments WHERE stripe_session_id = ?",
            "s",
            $id
        );

        return !empty($data) ? new Shipment($data[0]) : null;
    }

    public function insert(Shipment $shipment): Shipment
    {
        DatabaseHelper::preparedQuery(
            "INSERT INTO shipments 
            (sale_id, shippo_shipment_id, shippo_transaction_id, stripe_session_id, tracking_number, carrier, status, label_url)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            "isssssss",
            $shipment->getSaleId(),
            $shipment->getShippoShipmentId(),
            $shipment->getShippoTransactionId(),
            $shipment->getStripeSessionId(),
            $shipment->getTrackingNumber(),
            $shipment->getCarrier(),
            $shipment->getStatus(),
            $shipment->getLabelUrl()
        );

        $shipment->setId(DatabaseHelper::getLastId());

        return $shipment;
    }

    public function update(Shipment $shipment): Shipment
    {
        DatabaseHelper::preparedQuery(
            "UPDATE shipments 
            SET shippo_shipment_id=?, shippo_transaction_id=?, stripe_session_id=?, tracking_number=?, carrier=?, status=?, label_url=? 
            WHERE id=?",
            "sssssssi",
            $shipment->getShippoShipmentId(),
            $shipment->getShippoTransactionId(),
            $shipment->getStripeSessionId(),
            $shipment->getTrackingNumber(),
            $shipment->getCarrier(),
            $shipment->getStatus(),
            $shipment->getLabelUrl(),
            $shipment->getId()
        );

        return $shipment;
    }

    public function delete(int $id): bool
    {
        return DatabaseHelper::preparedQuery(
            "DELETE FROM shipments WHERE id = ?",
            "i",
            $id
        );
    }
}