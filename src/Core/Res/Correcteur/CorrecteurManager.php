<?php

namespace App\Core\Res\Correcteur;

use App\Core\Res\Grille\Grille;
use App\Core\Res\ProfilOuScore\ProfilOuScore;
use App\Entity\CandidatReponse;
use App\Entity\Correcteur;
use App\Entity\Session;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class CorrecteurManager
{

    /**
     * @param Grille $grille
     * @param ProfilOuScore $score
     * @param Correcteur $correcteur
     * @param Session $session
     * @return array
     */
    public function corriger(Grille $grille, ProfilOuScore $score, Correcteur $correcteur, Session $session): array
    {
        $expression_language = new ExpressionLanguage();

        $corrige = [];

        /** @var CandidatReponse $reponse */
        foreach ($session->reponses_candidats as $reponse) {
            $result = [];

            foreach ($score->getProperties() as $property) {

                // TODO ajouter les vraies valeurs
                $result[$property->nom_php] = $expression_language->evaluate(
                    $correcteur->values[$property->nom_php]
                );

            }

            $corrige[] = $result;
        }

        return $corrige;
    }

}