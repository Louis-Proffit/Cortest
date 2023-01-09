<?php

namespace Res\ProfilComputer;

use App\Core\Entities\EtalonnageComputer;
use App\Core\Entities\ProfilOuScore;
use Res\DefinitionScoreOuProfil\ProfilOuScoreCahierDesCharges;

class Etalonnage2CahierDesCharges extends EtalonnageComputer
{

    /**
     * @param ProfilOuScoreCahierDesCharges $score
     * @return ProfilOuScore
     */
    public function compute($score): ProfilOuScore
    {
        return new ProfilOuScoreCahierDesCharges(
            collationnement: $score->collationnement,
            verbal_mot: 3,
            spatial: 3,
            verbal_syntaxique: 3,
            raisonnement: 3,
            dic: 3,
            anxiete: 3,
            irritabilite: 3,
            impusilvite: 3,
            introspection: 3,
            entetement: 3,
            mefiance: 3,
            depression: 3,
            gene: 3,
            manque_altruisme: 3,
            sociabilite: 3,
            spontaneite: 3,
            ascendance: 3,
            assurance: 3,
            interet_intelletuel: 3,
            nouveaute: 3,
            creativite: 3,
            rigueur: 3,
            planification: 3,
            perseverance: 3,
            sincerite: 3,
            obsessionalite: 3,
            agressivite: 3,
            depressivite: 3,
            paranoidie: 3,
            narcissisme: 3,
            intolerance_a_la_frustration: 3
        );
    }
}