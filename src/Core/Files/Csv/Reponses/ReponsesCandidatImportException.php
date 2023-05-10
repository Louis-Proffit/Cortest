<?php

namespace App\Core\Files\Csv\Reponses;

use Exception;

class ReponsesCandidatImportException extends Exception
{

    public function __construct(string $message)
    {
        parent::__construct($message);
    }

}