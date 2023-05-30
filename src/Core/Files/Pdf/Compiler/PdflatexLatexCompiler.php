<?php

namespace App\Core\Files\Pdf\Compiler;

class PdflatexLatexCompiler implements LatexCompiler
{
    const EXECUTABLE_NAME = "pdflatex";
    const OPTIONS = "-interaction=nonstopmode -file-line-error";

    public function buildCommandLine(string $outputDirectoryPath, string $texInputFile): string
    {
        return self::EXECUTABLE_NAME . " " . self::OPTIONS . " --output-directory=\"" . $outputDirectoryPath . "\" \"" . $texInputFile . "\"";
    }
}