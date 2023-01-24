<?php

namespace App\Core\Correcteur;

use App\Core\Correcteur\ExpressionLanguage\CortestExpressionLanguage;
use App\Core\Correcteur\ExpressionLanguage\Environment\CortestExpressionEnvironment;
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

        foreach ($reponses_candidat as $reponse_candidat) {
            $result = [];

            $cortest_expression_environment = new CortestExpressionEnvironment(echelles: $correcteur->get_echelles_mapped_noms(),
                reponses: $reponse_candidat->reponses,
                cortest_expression_language: $this->cortest_expression_language);


            /** @var EchelleCorrecteur $echelle */
            foreach ($correcteur->echelles as $echelle) {
                $nom_echelle = $echelle->echelle->nom_php;
                $result[$nom_echelle] = $cortest_expression_environment->get_score($nom_echelle);
            }

            $corrige[$reponse_candidat->id] = $result;
        }

        return $corrige;
    }
}