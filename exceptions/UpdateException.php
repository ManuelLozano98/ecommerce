<?php
namespace App\Exceptions;

use Exception;

class UpdateException extends Exception {
    public function __construct($message = "Update operation failed") {
        parent::__construct($message);
    }

}