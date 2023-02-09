<?php

namespace App\Form\Data;

use DateTime;

class RechercheReponsesCandidat
{

    public string $filtre_prenom;
    public string $filtre_nom;
    public DateTime $filtre_date_de_naissance_min;
    public DateTime $filtre_date_de_naissance_max;

    /**
     * @var ReponseCandidatChecked[]
     */
    public array $reponses_candidat;

    /**
     * @param string $filtre_prenom
     * @param string $filtre_nom
     * @param DateTime $filtre_date_de_naissance_min
     * @param DateTime $filtre_date_de_naissance_max
     * @param ReponseCandidatChecked[] $reponses_candidat
     */
    public function __construct(string $filtre_prenom, string $filtre_nom, DateTime $filtre_date_de_naissance_min, DateTime $filtre_date_de_naissance_max, array $reponses_candidat)
    {
        $this->filtre_prenom = $filtre_prenom;
        $this->filtre_nom = $filtre_nom;
        $this->filtre_date_de_naissance_min = $filtre_date_de_naissance_min;
        $this->filtre_date_de_naissance_max = $filtre_date_de_naissance_max;
        $this->reponses_candidat = $reponses_candidat;
    }


}