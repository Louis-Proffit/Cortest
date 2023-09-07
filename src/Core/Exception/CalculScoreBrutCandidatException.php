<?php

namespace App\Core\Exception;

use App\Entity\ReponseCandidat;
use Exception;

class CalculScoreBrutCandidatException extends Exception
{

    public function __construct(public readonly ReponseCandidat $reponseCandidat, CalculScoreBrutException $previous)
    {
        parent::__construct(null, 0, $previous);
    }
}