<?php

namespace App\Core\IO\Correcteur;

use Symfony\Component\HttpFoundation\Session\Session;

interface ImportCorrecteurXMLErrorHandler
{

    public function handleError(string $message, int $line = 0, int $col = 0): void;

}