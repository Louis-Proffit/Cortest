<?php

namespace App\Core\Exception;

use Exception;

class NoReponsesCandidatException extends Exception
{

    public function __construct()
    {
        parent::__construct("Aucune réponse de candidat sélectionnée : impossible de trouver la session correspondante");
    }
}