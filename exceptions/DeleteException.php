<?php
namespace App\Exceptions;

use Exception;

class DeleteException extends Exception {
    public function __construct($message = "Delete operation failed") {
        parent::__construct($message);
    }

}