<?php

namespace App\Exceptions;

use Exception;

class ForeignKeyException extends Exception
{
    public function __construct($message = "The referenced foreign key does not exist")
    {
        parent::__construct($message);
    }
}
