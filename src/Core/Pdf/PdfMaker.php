<?php

namespace App\Core\Pdf;

class PdfMaker
{
    public function render($data, string $renderingFile, string $temp = "temp")
    {
        ob_start();
        include($renderingFile);
        if(file_exists("$temp".".tex")) {
            unlink("$temp" . ".tex");
        }
        file_put_contents($temp . ".tex", ob_get_clean());
        exec("pdflatex " . $temp . ".tex");
    }

    public function clean(string $temp = "temp")
    {
        unlink($temp . ".aux");
        unlink($temp . ".bcf");
        unlink($temp . ".log");
        unlink($temp . ".out");
        unlink($temp . ".tex");
        unlink($temp . ".pytxcode");
        unlink($temp . ".run.xml");
    }
}