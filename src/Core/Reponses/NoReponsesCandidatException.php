<?php

namespace App\Core\Reponses;

use Exception;

class NoReponsesCandidatException extends Exception
{

    public function __construct()
    {
        parent::__construct("Aucune réponse candidat : impossible de trouver la session correspondante");
    }

}