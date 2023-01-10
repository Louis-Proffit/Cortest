<?php

namespace App\Core\Res\Grille\Values;

use App\Core\Res\Grille\CortestGrille;
use App\Core\Res\Grille\CortestProperty;
use App\Core\Res\Grille\Grille;
use DateTime;

#[CortestGrille(nom: "Grille d'octobre 2019", tests: [])]
class GrilleOctobre2019 extends Grille
{

    #[CortestProperty(nom: "Nom")]
    public string $nom;

    #[CortestProperty(nom: "Prenom")]
    public string $prenom;

    #[CortestProperty(nom: "Nom")]
    public string $nom_jeune_fille;

    #[CortestProperty(nom: "Niveau scolaire")]
    public string $niveau_scolaire;

    #[CortestProperty(nom: "Date de naissance")]
    public DateTime $date_naissance;

    #[CortestProperty(nom: "Sexe")]
    public int $sexe;

    #[CortestProperty(nom: "Concours")]
    public int $concours;

    #[CortestProperty(nom: "SGAP")]
    public int $sgap;

    #[CortestProperty(nom: "Date d'examen")]
    public DateTime $date_examen;

    #[CortestProperty(nom: "Type de concours")]
    public int $type_concours;

    #[CortestProperty(nom: "Version batterie")]
    public int $version_batterie;

    #[CortestProperty(nom: "Réserve")]
    public int $reserve;

    #[CortestProperty(nom: "Autre 1")]
    public int $autre_1;

    #[CortestProperty(nom: "Autre 2")]
    public int $autre_2;

    protected function getClass(): string
    {
        return GrilleOctobre2019::class;
    }

    public function fill(array $raw): void
    {
        parent::fill($raw);
        $this->nom = $raw["nom"];
        $this->prenom = $raw["prenom"];
    }
}