<?php

namespace App\Core\Res\ProfilGraphique;

use App\Core\Res\ProfilGraphique\Values\ProfilGraphiqueBatonnetCahierDesCharges;

class ProfilGraphiqueRepository
{

    private array $values;

    public function __construct(
        ProfilGraphiqueBatonnetCahierDesCharges $profil_graphique_cahier_des_charges
    )
    {
        $this->values = [
            $profil_graphique_cahier_des_charges
        ];
    }

    public function sample(): ProfilGraphique
    {
        return $this->values[0];
    }

    public function get(int $index): ProfilGraphique
    {
        return $this->values[$index];
    }

    public function nomToIndex(): array
    {
        $result = [];

        foreach ($this->values as $index => $profil_graphique) {
            $result[$profil_graphique->getNom()] = $index;
        }

        return $result;
    }

    public function nomToProfilGraphique(): array
    {
        $result = [];

        foreach ($this->values as $profil_graphique) {
            $result[$profil_graphique->getNom()] = $profil_graphique;
        }

        return $result;
    }

}