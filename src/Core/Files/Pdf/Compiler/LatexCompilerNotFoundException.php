<?php

namespace App\Core\Files\Pdf\Compiler;

use App\Controller\Exception\LatexCompilationFailedExceptionEventListener;
use Exception;


class LatexCompilerNotFoundException extends Exception
{

    /**
     * @param LatexCompiler $latexCompiler
     */
    public function __construct(
        public readonly LatexCompiler $latexCompiler,
    )
    {
        parent::__construct("Le compilateur n'existe pas : " . $this->latexCompiler::class);
    }

}