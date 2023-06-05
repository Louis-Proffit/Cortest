<?php

namespace App\Core\Files\Pdf\Merger;

use Symfony\Component\Process\Process;

interface PdfMerger
{
    public function checkExists(): bool;

    /**
     * @param string[] $toMerge
     * @param string $output
     * @return Process
     */
    public function mergerProcess(array $toMerge, string $output): Process;
}