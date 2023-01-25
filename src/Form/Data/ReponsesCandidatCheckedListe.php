<?php

namespace App\Form\Data;

class ReponsesCandidatCheckedListe
{

    /**
     * @var ReponsesCandidatChecked[]
     */
    public array $reponses_candidat;

    /**
     * @param ReponsesCandidatChecked[] $reponses_candidat
     */
    public function __construct(array $reponses_candidat)
    {
        $this->reponses_candidat = $reponses_candidat;
    }
}