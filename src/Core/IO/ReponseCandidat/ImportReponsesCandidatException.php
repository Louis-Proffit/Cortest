<?php

namespace App\Core\IO\ReponseCandidat;

use Exception;

class ImportReponsesCandidatException extends Exception
{

    public function __construct(string $message)
    {
        parent::__construct($message);
    }

}