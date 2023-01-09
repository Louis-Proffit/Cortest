<?php

namespace App\Core\Res\ProfilGraphique\Values;

use App\Core\Res\ProfilGraphique\ProfilGraphique;
use App\Core\Res\ProfilOuScore\ProfilOuScore;
use App\Core\Res\ProfilOuScore\Values\ProfilOuScoreCahierDesCharges;

class ProfilGraphiqueBatonnetCahierDesCharges implements ProfilGraphique
{

    public function __construct(
        private readonly ProfilOuScoreCahierDesCharges $profil_ou_score_cahier_des_charges
    )
    {
    }

    public function getTemplate(): string
    {
        return "profil_graphique/batonnet_cahier_des_charges.tex.twig";
    }

    public function getNom(): string
    {
        return "Batonnets format Cahier des Charges";
    }

    public function getProfilOuScore(): ProfilOuScore
    {
        return $this->profil_ou_score_cahier_des_charges;
    }
}