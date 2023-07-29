<?php

namespace App\Core\Correcteur;

use App\Core\Correcteur\ExpressionLanguage\CortestExpressionLanguage;
use App\Core\Correcteur\ExpressionLanguage\Environment\CortestEvaluationEnvironment;
use App\Entity\Correcteur;
use App\Entity\EchelleCorrecteur;
use App\Entity\ReponseCandidat;

readonly class CorrecteurManager
{


    public function __construct(
        private CortestExpressionLanguage $cortestExpressionLanguage
    )
    {
    }

    /**
     * @param Correcteur $correcteur
     * @param ReponseCandidat[] $reponseCandidats
     * @return array
     */
    public function corriger(Correcteur $correcteur, array $reponseCandidats): array
    {
        $corrige = [];

        $echelles = $correcteur->echelles->toArray();

        $types = array_combine(
            array_map(fn(EchelleCorrecteur $e) => $e->echelle->nom_php, $echelles),
            array_map(fn(EchelleCorrecteur $e) => $e->echelle->type, $echelles)
        );

        $expressions = array_combine(
            array_map(fn(EchelleCorrecteur $e) => $e->echelle->nom_php, $echelles),
            array_map(fn(EchelleCorrecteur $e) => $e->expression, $echelles)
        );

        foreach ($reponseCandidats as $reponseCandidat) {

            $cortestEvaluationEnvironment = new CortestEvaluationEnvironment(
                reponses: $reponseCandidat->reponses,
                types: $types,
                expressions: $expressions,
                cortest_expression_language: $this->cortestExpressionLanguage);


            $corrige[$reponseCandidat->id] = $cortestEvaluationEnvironment->compute_scores();
        }

        return $corrige;
    }
}