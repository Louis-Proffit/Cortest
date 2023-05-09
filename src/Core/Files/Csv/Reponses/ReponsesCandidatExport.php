<?php

namespace App\Core\Files\Csv\Reponses;

use App\Entity\QuestionConcours;
use App\Entity\ReponseCandidat;

class ReponsesCandidatExport
{

    const NOM_KEY = "Nom";
    const PRENOM_KEY = "Prenom";
    const NOM_JEUNE_FILLE_KEY = "Nom de jeune fille";
    const NIVEAU_SCOLAIRE_KEY = "Niveau scolaire";
    const DATE_DE_NAISSANCE_KEY = "Date de naissance";
    const SEXE_KEY = "Sexe";
    const RESERVE_KEY = "Reserve";
    const AUTRE_1_KEY = "Autre 1";
    const AUTRE_2_KEY = "Autre 2";
    const CODE_BARRE_KEY = "Code barre";
    const EIRS_KEY = "EIRS";
    const REPONSE_PREFIX_KEY = "Reponse ";

    const HOMME_VALUE = "Homme";
    const FEMME_VALUE = "Femme";
    const INDEX_TO_VALUE_SEXE = [ReponseCandidat::INDEX_HOMME => self::HOMME_VALUE, ReponseCandidat::INDEX_FEMME => self::FEMME_VALUE];
    const VALUE_TO_INDEX_SEXE = [self::HOMME_VALUE => ReponseCandidat::INDEX_HOMME, self::FEMME_VALUE => ReponseCandidat::INDEX_FEMME];

    const DATE_FORMAT = "d/m/Y";

    const REPONSE_INDEX_TO_VALUE = [0 => "", 1 => "A", 2 => "B", 3 => "C", 4 => "D", 5 => "E"];
    const REPONSE_VALUE_TO_INDEX = ["" => 0, "A" => 1, "B" => 2, "C" => 3, "D" => 4, "E" => 5];

    /**
     * @param ReponseCandidat[] $reponses
     * @return array
     */
    public function export(array $reponses): array
    {

        $data = [];

        if (!empty($reponses)) {
            /** @var QuestionConcours[] $questions */
            $questions = $reponses[0]->session->concours->questions;

            foreach ($reponses as $reponse) {
                $toAdd = [
                    self::NOM_KEY => $reponse->nom,
                    self::PRENOM_KEY => $reponse->prenom,
                    self::NOM_JEUNE_FILLE_KEY => $reponse->nom_jeune_fille,
                    self::NIVEAU_SCOLAIRE_KEY => $reponse->niveau_scolaire->nom,
                    self::DATE_DE_NAISSANCE_KEY => $reponse->date_de_naissance->format(self::DATE_FORMAT),
                    self::SEXE_KEY => self::INDEX_TO_VALUE_SEXE[$reponse->sexe],
                    self::RESERVE_KEY => $reponse->reserve,
                    self::AUTRE_1_KEY => $reponse->autre_1,
                    self::AUTRE_2_KEY => $reponse->autre_2,
                    self::CODE_BARRE_KEY => $reponse->code_barre,
                    self::EIRS_KEY => $reponse->eirs,
                ];

                foreach ($questions as $question) {

                    if (key_exists($question->indice, $reponse->reponses)) {
                        $formattedResponse = self::REPONSE_INDEX_TO_VALUE[$reponse->reponses[$question->indice]];
                    } else {
                        $formattedResponse = self::REPONSE_INDEX_TO_VALUE[0];
                    }

                    $toAdd[self::questionColumnName($question->indice)] = $formattedResponse;
                }

                $data[] = $toAdd;
            }
        }

        return $data;
    }


    /**
     * Produit le nom de la colonne associée à l'indice d'une question. A utiliser de façon identique entre l'import et l'export
     * @param int $indice
     * @return string
     */
    public static function questionColumnName(int $indice): string
    {
        return self::REPONSE_PREFIX_KEY . " " . $indice;
    }
}