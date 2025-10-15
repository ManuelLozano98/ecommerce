<?php
namespace App\Exceptions;

use Exception;

class InsertException extends Exception {
    public function __construct($message = "Insert operation failed") {
        parent::__construct($message);
    }

}