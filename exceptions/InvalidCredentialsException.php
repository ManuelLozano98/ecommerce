<?php

namespace App\Exceptions;

use Exception;

class InvalidCredentialsException extends Exception
{
    public function __construct($message = "Invalid credentials")
    {
        parent::__construct($message);
    }
}
