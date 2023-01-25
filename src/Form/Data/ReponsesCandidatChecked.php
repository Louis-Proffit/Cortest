<?php

namespace App\Form\Data;

use App\Entity\ReponseCandidat;

class ReponsesCandidatChecked
{

    public ReponseCandidat $reponses_candidat;
    public bool $checked;

    /**
     * @param ReponseCandidat $reponses_candidat
     * @param bool $checked
     */
    public function __construct(ReponseCandidat $reponses_candidat, bool $checked)
    {
        $this->reponses_candidat = $reponses_candidat;
        $this->checked = $checked;
    }


}