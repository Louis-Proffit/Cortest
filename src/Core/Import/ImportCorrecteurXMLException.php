<?php

namespace App\Core\Import;

use App\Entity\Correcteur;
use App\Repository\ProfilRepository;
use Exception;

class ImportCorrecteurXMLException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message,);
    }
}