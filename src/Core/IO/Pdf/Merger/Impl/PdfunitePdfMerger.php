<?php

namespace App\Core\IO\Pdf\Merger\Impl;

use App\Core\IO\Pdf\Merger\PdfMerger;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class PdfunitePdfMerger implements PdfMerger
{

    const EXECUTABLE_NAME = "pdfunite";

    public function checkExists(): bool
    {
        $finder = new ExecutableFinder();
        return $finder->find(self::EXECUTABLE_NAME);
    }

    public function mergerProcess(array $toMerge, string $output): Process
    {
        $command = [self::EXECUTABLE_NAME];
        $command = array_merge($command, $toMerge);
        $command[] = $output;
        return new Process($command);
    }
}