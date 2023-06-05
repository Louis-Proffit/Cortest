<?php

namespace App\Core\Files\Pdf\Compiler;

use App\Controller\Exception\LatexCompilationFailedExceptionEventListener;
use App\Entity\ReponseCandidat;
use Exception;

/**
 * Exception lancée par l'échec d'une compilation Latex.
 * @see LatexCompilationFailedExceptionEventListener
 */
class LatexCompilationFailedException extends Exception
{

    /**
     * @param ReponseCandidat $reponseCandidat le candidat pour lequel le rendu pdf a échoué
     * @param string|null $logFilePath le chemin vers le fichier de log associé à la compilation échouée
     */
    public function __construct(
        public readonly ReponseCandidat $reponseCandidat,
        public readonly string|null     $logFilePath
    )
    {
        parent::__construct("Echec de la compilation, informations de log dans le fichier suivant : $this->logFilePath");
    }

}