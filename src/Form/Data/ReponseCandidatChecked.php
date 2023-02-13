<?php

namespace App\Form\Data;

use App\Entity\ReponseCandidat;

class ReponseCandidatChecked
{

    public ReponseCandidat $reponse_candidat;
    public bool $checked;
    /**
     * @param ReponseCandidat $reponse_candidat
     * @param bool $checked
     */
    public function __construct(ReponseCandidat $reponse_candidat, bool $checked)
    {
        $this->reponse_candidat = $reponse_candidat;
        $this->checked = $checked;
    }


}