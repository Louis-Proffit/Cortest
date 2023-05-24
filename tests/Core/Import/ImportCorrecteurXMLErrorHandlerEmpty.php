<?php

namespace App\Tests\Core\Import;

use App\Core\IO\Correcteur\ImportCorrecteurXMLErrorHandler;

class ImportCorrecteurXMLErrorHandlerEmpty implements ImportCorrecteurXMLErrorHandler
{

    public function handleError(string $message, int $line = 0, int $col = 0): void
    {
        // TODO: Implement handleError() method.
    }
}