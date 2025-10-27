<?php

namespace App\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    public function __construct($message = "The record was not found or does not exist")
    {
        parent::__construct($message);
    }
}
