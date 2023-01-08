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

    public function fill(array $raw)
    {
        $this->reponses = $this->getOrDefault("reponses", $raw, array_fill(1, 10, '@'));
        $this->nom = $this->getOrDefault("nom", $raw, "");
        $this->prenom = $this->getOrDefault("prenom", $raw, "");
        $this->nom_jeune_fille = $this->getOrDefault("nom_jeune_fille", $raw, '');
    }
}
