<?php

namespace App\Services;

use Shippo;


class ShippoService
{
    public function __construct(string $key)
    {
        Shippo::setApiKey($key);
    }
    public function get()
    {
        return Shippo::getApiKey();
    }
}
