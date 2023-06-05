<?php

namespace App\Core\Files\Pdf\Compiler\Impl;

use App\Core\Files\Pdf\Compiler\LatexCompiler;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;


class PdflatexLatexCompiler implements LatexCompiler
{
    const EXECUTABLE_NAME = "pdflatex";

    public function compilerProcess(string $outputDirectoryPath, string $texInputFile): Process
    {
        return new Process([self::EXECUTABLE_NAME, '-interaction=nonstopmode', '-file-line-error', '--output-directory=' . $outputDirectoryPath, $texInputFile]);
    }

    public function checkExists(): bool
    {
        $finder = new ExecutableFinder();
        return $finder->find(self::EXECUTABLE_NAME) != null;
    }
}