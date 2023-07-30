<?php

namespace App\Core\IO\Pdf\Compiler;

use Symfony\Component\Process\Process;

interface LatexCompiler
{
    public function checkExists(): bool;

    public function compilerProcess(string $outputDirectoryPath, string $texInputFile): Process;

}