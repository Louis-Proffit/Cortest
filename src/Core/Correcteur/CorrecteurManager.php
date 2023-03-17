<?php

namespace App\Core\Correcteur;

use App\Core\Correcteur\ExpressionLanguage\CortestExpressionLanguage;
use App\Core\Correcteur\ExpressionLanguage\Environment\CortestEvaluationEnvironment;
use App\Entity\Correcteur;
use App\Entity\EchelleCorrecteur;
use App\Entity\ReponseCandidat;

class CorrecteurManager
{


    public function __construct(
        private readonly CortestExpressionLanguage $cortest_expression_language
    )
    {
    }

    /**
     * @param Correcteur $correcteur
     * @param ReponseCandidat[] $reponses_candidat
     * @return array
     */
    public function corriger(Correcteur $correcteur, array $reponses_candidat): array
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

        foreach ($reponses_candidat as $reponse_candidat) {

            $cortest_expression_environment = new CortestEvaluationEnvironment(
                reponses: $reponse_candidat->reponses,
                types: $types,
                expressions: $expressions,
                cortest_expression_language: $this->cortest_expression_language);


            $corrige[$reponse_candidat->id] = $cortest_expression_environment->compute_scores();

            // dump($reponse_candidat->reponses);
            // dump($corrige[$reponse_candidat->id]);
        }

        return $corrige;
    }
}