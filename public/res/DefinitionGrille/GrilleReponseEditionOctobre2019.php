<?php

namespace Res\DefinitionGrille;

use App\Core\Entities\GrilleReponse;

class GrilleReponseEditionOctobre2019 extends GrilleReponse
{
    public string $nom;
    public string $prenom;
    public string|null $nom_jeune_fille;
    public int $date_naissance_jour;
    public int $date_naissance_mois;
    public int $date_naissance_an;
    public int $sexe;
    public string $concours;
    public int $sgap;
    public int $date_concours_jour;
    public int $date_concours_mois;
    public int $date_concours_an;
    public int $type_concours;
    public int $version_batterie;
    public int $reserve;
    public int $autre_1;
    public int $autre_2;

    public function fill(string $raw)
    {
        $this->reponses = array_fill(1, 640, '@');
        $this->nom = "Proffit";
        $this->prenom = "Louis";
        $this->nom_jeune_fille = "Oui";
    }
}
