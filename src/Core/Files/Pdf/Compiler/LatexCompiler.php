<?php

namespace App\Core\Files\Pdf\Compiler;

use Symfony\Component\Process\Process;

interface LatexCompiler
{
    public function checkExists(): bool;

    public function compilerProcess(string $outputDirectoryPath, string $texInputFile): Process;

}