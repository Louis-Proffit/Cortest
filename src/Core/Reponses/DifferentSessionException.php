<?php

namespace App\Core\Reponses;

use Exception;

class DifferentSessionException extends Exception
{

    public function __construct()
    {
        parent::__construct("Les réponses de candidats sélectionnés correspondent à des session différentes.");
    }

}