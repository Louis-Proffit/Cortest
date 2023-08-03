<?php

namespace App\Core\ReponseCandidat;

use App\Entity\QuestionTest;
use App\Entity\ReponseCandidat;

class ExportReponseCandidat
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

    const HOMME_VALUE = "Homme";
    const FEMME_VALUE = "Femme";
    const INDEX_TO_VALUE_SEXE = [ReponseCandidat::INDEX_HOMME => self::HOMME_VALUE, ReponseCandidat::INDEX_FEMME => self::FEMME_VALUE];
    const VALUE_TO_INDEX_SEXE = [self::HOMME_VALUE => ReponseCandidat::INDEX_HOMME, self::FEMME_VALUE => ReponseCandidat::INDEX_FEMME];

    const DATE_FORMAT = "d/m/Y";

    /**
     * Si les questions sont "null", les reponses du candidat aux questions ne sont pas incluses dans l'export
     * @param ReponseCandidat $reponsesCandidat
     * @param QuestionTest[]|null $questions
     * @return array
     */
    public function exportCandidat(ReponseCandidat $reponsesCandidat): array
    {
        return [
            self::NOM_KEY => $reponsesCandidat->nom,
            self::PRENOM_KEY => $reponsesCandidat->prenom,
            self::NOM_JEUNE_FILLE_KEY => $reponsesCandidat->nom_jeune_fille,
            self::NIVEAU_SCOLAIRE_KEY => $reponsesCandidat->niveau_scolaire->nom,
            self::DATE_DE_NAISSANCE_KEY => $reponsesCandidat->date_de_naissance->format(self::DATE_FORMAT),
            self::SEXE_KEY => self::INDEX_TO_VALUE_SEXE[$reponsesCandidat->sexe],
            self::RESERVE_KEY => $reponsesCandidat->reserve,
            self::AUTRE_1_KEY => $reponsesCandidat->autre_1,
            self::AUTRE_2_KEY => $reponsesCandidat->autre_2,
            self::CODE_BARRE_KEY => $reponsesCandidat->code_barre,
            self::EIRS_KEY => $reponsesCandidat->eirs,
        ];
    }

    /**
     * @param ReponseCandidat $reponseCandidat
     * @param QuestionTest[] $questions
     * @return array
     * @throws \Exception
     */
    public function exportReponses(ReponseCandidat $reponseCandidat, array $questions): array
    {
        $result = [];

        if ($questions != null) {
            foreach ($questions as $question) {

                $trueIndex = $question->indice - 1;
                if (key_exists($trueIndex, $reponseCandidat->reponses)) {
                    $reponseInt = $reponseCandidat->reponses[$trueIndex];
                    $formattedResponse = ReponseCandidat::REPONSES_INDEX_TO_NOM[$reponseInt];
                } else {
                    throw new \Exception("Missing question index " . $question->indice);
                }

                $result[$question->abreviation] = $formattedResponse;
            }
        }

        return $result;
    }

    public function exportCandidatAndReponses(ReponseCandidat $reponseCandidat, array $questions): array
    {
        return array_merge(
            $this->exportCandidat($reponseCandidat),
            $this->exportReponses($reponseCandidat, $questions)
        );
    }
}