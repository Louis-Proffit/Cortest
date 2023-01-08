<?php

namespace App\Core\Computer;

use App\Core\Entities\ProfilOuScore;
use App\Entity\CandidatReponse;
use App\Entity\DefinitionProfilComputer;
use App\Entity\DefinitionGrille;
use App\Entity\DefinitionScoreComputer;
use App\Repository\RuntimeResourcesRepository;

class GrilleComputer
{

    public function __construct(
        private readonly RuntimeResourcesRepository $repository
    )
    {
    }

    /**
     * @param CandidatReponse[] $candidatReponses
     * @param DefinitionGrille $definition_grille
     * @return array
     */
    public function computeAll(
        array            $candidatReponses,
        DefinitionGrille $definition_grille): array
    {
        $grilleReponse = $this->repository->definitionGrille($definition_grille);

        $result = [];
        foreach ($candidatReponses as $candidatReponse) {
            $grilleReponse = clone $grilleReponse;
            $grilleReponse->fill($candidatReponse->reponses);
            $result[] = $grilleReponse;
        }

        return $result;
    }

}