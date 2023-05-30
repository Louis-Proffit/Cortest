<?php

namespace App\Core\Files\Pdf\Compiler;

interface LatexCompiler
{

    public function buildCommandLine(string $texInputFile, string $outputDirectoryPath): string;

}