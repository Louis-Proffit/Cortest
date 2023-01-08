<?php

namespace App\Core\Computer;

use App\Core\Entities\GrilleReponse;
use App\Entity\DefinitionScoreComputer;
use App\Repository\RuntimeResourcesRepository;

class ScoreComputer
{

    public function __construct(
        private readonly RuntimeResourcesRepository $repository
    )
    {
    }

    /**
     * @param GrilleReponse[] $grilles
     * @param DefinitionScoreComputer $definition_score_computer
     * @return array
     */
    public function computeAll(
        array                   $grilles,
        DefinitionScoreComputer $definition_score_computer): array
    {
        $scoreComputer = $this->repository->scoreComputer($definition_score_computer);

        $result = [];
        foreach ($grilles as $grille) {
            $result[] = $scoreComputer->compute($grille);
        }

        return $result;
    }

}