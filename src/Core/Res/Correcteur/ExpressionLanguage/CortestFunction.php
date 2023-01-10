<?php

namespace App\Core\Res\Correcteur\ExpressionLanguage;

interface CortestFunction
{

    public function nom(): string;

    public function nom_php(): string;

    public function description(): string;

    public function evaluator(): callable;

}