<?php

namespace App\Core\Exception;

use Exception;
use Throwable;

class MissingEchelleException extends CalculScoreBrutException
{

    public function __construct(
        readonly mixed $entity,
        string         $message = ""
    )
    {
        parent::__construct($message);
    }
}