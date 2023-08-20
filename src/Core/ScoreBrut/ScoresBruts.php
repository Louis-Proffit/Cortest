<?php

namespace App\Core\ScoreBrut;

use App\Entity\Correcteur;
use App\Entity\ReponseCandidat;

class ScoresBruts
{

    /**
     * @param ScoreBrut[] $__array
     */
    public function __construct(
        readonly Correcteur $correcteur,
        private array               $__array = []
    )
    {
    }

    public function get(ReponseCandidat $reponseCandidat): ScoreBrut
    {
        return $this->__array[$reponseCandidat->id];
    }

    public function set(ReponseCandidat $reponseCandidat, ScoreBrut $scoreBrut): void
    {
        $this->__array[$reponseCandidat->id] = $scoreBrut;
    }

    /**
     * @return ScoreBrut[]
     */
    public function getAll(): array
    {
        return $this->__array;
    }
}