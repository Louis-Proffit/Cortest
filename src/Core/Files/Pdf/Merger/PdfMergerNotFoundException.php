<?php

namespace App\Core\Files\Pdf\Merger;

use Exception;


class PdfMergerNotFoundException extends Exception
{

    /**
     * @param PdfMerger $pdfMerger
     */
    public function __construct(
        public readonly PdfMerger $pdfMerger,
    )
    {
        parent::__construct("Le merger n'existe pas : " . $this->pdfMerger::class);
    }

}