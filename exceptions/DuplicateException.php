<?php

namespace App\Exceptions;

use Exception;

class DuplicateException extends Exception
{
    public function __construct($message = "A record with the same unique value already exists")
    {
        parent::__construct($message);
    }
}
