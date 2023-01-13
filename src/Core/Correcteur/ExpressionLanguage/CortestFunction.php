<?php

namespace App\Core\Correcteur\ExpressionLanguage;

interface CortestFunction
{

    public function nom(): string;

    public function nom_php(): string;

    public function description(): string;

    public function evaluator(): callable;

}