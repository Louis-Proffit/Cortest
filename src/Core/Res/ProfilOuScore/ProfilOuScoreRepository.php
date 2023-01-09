<?php

namespace App\Core\Res\ProfilOuScore;

use App\Core\Res\ProfilOuScore\Values\ProfilOuScoreCahierDesCharges;

class ProfilOuScoreRepository
{

    private array $values;

    public function __construct(
        ProfilOuScoreCahierDesCharges $profil_ou_score_cahier_des_charges
    )
    {
        $this->values = [
            $profil_ou_score_cahier_des_charges
        ];
    }

    public function sample(): ProfilOuScore
    {
        return $this->values[0];
    }

    public function get(int $index): ProfilOuScore
    {
        return $this->values[$index];
    }

    public function all(): array
    {
        return $this->values;
    }

    public function nomToIndex(): array
    {
        $result = [];

        foreach ($this->values as $index => $profil_ou_score) {
            $result[$profil_ou_score->getNom()] = $index;
        }

        return $result;
    }

    public function nomToProfilOuScore(): array
    {
        $result = [];

        foreach ($this->values as $profil_ou_score) {
            $result[$profil_ou_score->getNom()] = $profil_ou_score;
        }

        return $result;
    }

}