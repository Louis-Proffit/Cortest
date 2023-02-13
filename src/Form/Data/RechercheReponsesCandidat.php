<?php

namespace App\Form\Data;

class RechercheReponsesCandidat
{
    /**
     * @var ReponseCandidatChecked[]
     */
    public array $reponses_candidat;

    /**
     * @param ReponseCandidatChecked[] $reponses_candidat
     */
    public function __construct(array $reponses_candidat)
    {
        $this->reponses_candidat = $reponses_candidat;
    }


}