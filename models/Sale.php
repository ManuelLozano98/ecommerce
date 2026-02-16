<?php

namespace App\Models;

use App\Enums\PaymentMethods;
use App\Enums\SaleStatus;
use JsonSerializable;

class Sale implements JsonSerializable
{
    private int $id;
    private int $user_id;
    private float $total_amount;
    // private string $payment_method;
    // private string $status;
    private string $created_at;
    private string $updated_at;
    private PaymentMethods $payment_method;
    private SaleStatus $status;
    private array $items = [];

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }


    function __construct($data = [])
    {
        $this->id = $data['id'] ?? 0;
        $this->user_id = $data['user_id'] ?? 0;
        $this->total_amount = $data['total_amount'] ?? 0.00;
        $this->payment_method = isset($data['payment_method'])
            ? PaymentMethods::tryFrom(str_replace(" ", "_", strtolower($data['payment_method']))) ?? PaymentMethods::CREDIT_CARD
            : PaymentMethods::CREDIT_CARD;
        $this->status = isset($data['status'])
            ? SaleStatus::tryFrom(str_replace(" ", "_", strtolower($data['status']))) ?? SaleStatus::PENDING
            : SaleStatus::PENDING;
        $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
        $this->updated_at = $data['updated_at'] ?? '';
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'payment_method' => $this->getPaymentMethod(),
            'status' => $this->getStatus(),
            'total_amount' => $this->total_amount,
            'updated_at' => $this->updated_at,
            'items' => $this->items
        ];
    }

    public function getId()
    {
        return $this->id;
    }
    public function getUserId()
    {
        return $this->user_id;
    }
    public function getTotalAmount()
    {
        return $this->total_amount;
    }
    public function getPaymentMethod()
    {
        return str_replace("_", " ", ucwords($this->payment_method->value, "_"));
    }
    public function getStatus()
    {
        return str_replace("_", " ", ucwords($this->status->value, "_"));
    }
    public function getCreatedAt()
    {
        return $this->created_at;
    }
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }
    public function setTotalAmount($amount)
    {
        $this->total_amount = $amount;
    }
    public function setPaymentMethod($method)
    {
        $this->payment_method = PaymentMethods::tryFrom(strtolower($method)) ?? PaymentMethods::CREDIT_CARD;
    }
    public function setStatus($status)
    {
        $this->status = SaleStatus::tryFrom(strtolower($status)) ?? SaleStatus::PENDING;
    }
    public function setCreatedAt($datetime)
    {
        $this->created_at = $datetime;
    }
    public function setUpdatedAt($datetime)
    {
        $this->updated_at = $datetime;
    }
    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
