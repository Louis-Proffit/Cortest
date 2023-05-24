<?php

namespace App\Form\Data;

use App\Entity\NiveauScolaire;
use App\Entity\Session;
use DateTime;

class RechercheFiltre
{

    public string $filtre_prenom;
    public string $filtre_nom;
    public DateTime $filtre_date_de_naissance_min;
    public DateTime $filtre_date_de_naissance_max;
    public ?NiveauScolaire $niveau_scolaire;
    public ?Session $session;

    /**
     * @param string $filtre_prenom
     * @param string $filtre_nom
     * @param DateTime $filtre_date_de_naissance_min
     * @param DateTime $filtre_date_de_naissance_max
     * @param NiveauScolaire|null $niveau_scolaire
     * @param Session|null $session
     */
    public function __construct(string $filtre_prenom, string $filtre_nom, DateTime $filtre_date_de_naissance_min, DateTime $filtre_date_de_naissance_max, ?NiveauScolaire $niveau_scolaire, ?Session $session)
    {
        $this->filtre_prenom = $filtre_prenom;
        $this->filtre_nom = $filtre_nom;
        $this->filtre_date_de_naissance_min = $filtre_date_de_naissance_min;
        $this->filtre_date_de_naissance_max = $filtre_date_de_naissance_max;
        $this->niveau_scolaire = $niveau_scolaire;
        $this->session = $session;
    }


}