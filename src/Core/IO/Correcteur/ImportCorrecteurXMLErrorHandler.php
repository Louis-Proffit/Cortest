<?php

namespace App\Core\IO\Correcteur;

use Symfony\Component\HttpFoundation\Session\Session;

class ImportCorrecteurXMLErrorHandler
{

    public function __construct(
        private readonly Session $session
    )
    {
    }

    public function handleError(string $message, int $line = 0, int $col = 0): void
    {
        $this->session->getFlashBag()->add("danger", "$message (ligne $line, colonne $col)");
    }

}