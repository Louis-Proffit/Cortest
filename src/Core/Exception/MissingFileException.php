<?php

namespace App\Core\Exception;

use Exception;
use Throwable;

class MissingFileException extends Exception
{

    public function __construct(
        readonly mixed $entity,
        string         $message = "",
        int            $code = 0,
        Throwable|null $throwable = null
    )
    {
        parent::__construct($message, $code, $throwable);
    }
}