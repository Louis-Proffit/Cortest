<?php

namespace App\Core\IO\Correcteur;

interface ImportCorrecteurXMLErrorHandler
{

    public function handleError(string $message, int $line = 0, int $col = 0): void;

}