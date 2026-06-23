<?php

namespace App\Models;

use JsonSerializable;

class Shipment implements JsonSerializable
{
    private int $id;
    private int $sale_id;

    private string $shippo_shipment_id;
    private string $shippo_transaction_id;
    private string $stripe_session_id;

    private ?string $tracking_number;
    private ?string $carrier;

    private string $status;

    private ?string $label_url;

    function __construct($data = [])
    {
        $this->id = $data['id'] ?? 0;
        $this->sale_id = $data['sale_id'] ?? 0;

        $this->shippo_shipment_id = $data['shippo_shipment_id'] ?? '';
        $this->shippo_transaction_id = $data['shippo_transaction_id'] ?? '';
        $this->stripe_session_id = $data['stripe_session_id'] ?? '';

        $this->tracking_number = $data['tracking_number'] ?? null;
        $this->carrier = $data['carrier'] ?? null;

        $this->status = $data['status'] ?? 'created';

        $this->label_url = $data['label_url'] ?? null;
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'sale_id' => $this->sale_id,
            'shippo_shipment_id' => $this->shippo_shipment_id,
            'shippo_transaction_id' => $this->shippo_transaction_id,
            'stripe_session_id' => $this->stripe_session_id,
            'tracking_number' => $this->tracking_number,
            'carrier' => $this->carrier,
            'status' => $this->status,
            'label_url' => $this->label_url,
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

    public function getSaleId()
    {
        return $this->sale_id;
    }
    public function setSaleId($sale_id)
    {
        $this->sale_id = $sale_id;
        return $this;
    }

    public function getShippoShipmentId()
    {
        return $this->shippo_shipment_id;
    }
    public function setShippoShipmentId($id)
    {
        $this->shippo_shipment_id = $id;
        return $this;
    }

    public function getShippoTransactionId()
    {
        return $this->shippo_transaction_id;
    }
    public function setShippoTransactionId($id)
    {
        $this->shippo_transaction_id = $id;
        return $this;
    }

    public function getStripeSessionId()
    {
        return $this->stripe_session_id;
    }
    public function setStripeSessionId($id)
    {
        $this->stripe_session_id = $id;
        return $this;
    }

    public function getTrackingNumber()
    {
        return $this->tracking_number;
    }
    public function setTrackingNumber($tracking)
    {
        $this->tracking_number = $tracking;
        return $this;
    }

    public function getCarrier()
    {
        return $this->carrier;
    }
    public function setCarrier($carrier)
    {
        $this->carrier = $carrier;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getLabelUrl()
    {
        return $this->label_url;
    }
    public function setLabelUrl($url)
    {
        $this->label_url = $url;
        return $this;
    }
}
