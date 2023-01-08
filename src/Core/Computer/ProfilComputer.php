<?php

namespace App\Core\Computer;

use App\Core\Entities\ProfilOuScore;
use App\Entity\CandidatReponse;
use App\Entity\DefinitionProfilComputer;
use App\Entity\DefinitionGrille;
use App\Entity\DefinitionScoreComputer;
use App\Repository\RuntimeResourcesRepository;

class ProfilComputer
{

    public function __construct(
        private readonly RuntimeResourcesRepository $repository,
    )
    {
    }

    /**
     * @param ProfilOuScore[] $scores
     * @param DefinitionProfilComputer $definition_etalonnage_computer
     * @return array
     */
    public function computeAll(
        array                    $scores,
        DefinitionProfilComputer $definition_etalonnage_computer): array
    {
        $etalonnageComputer = $this->repository->etalonnageComputer($definition_etalonnage_computer);

        $result = [];
        foreach ($scores as $score) {
            $result[] = $etalonnageComputer->compute($score);
        }

        return $result;
    }

}