<?php

namespace App\Core\Files\Csv\Reponses;

use App\Core\Files\Csv\Reponses\ReponsesCandidatExport;
use App\Entity\QuestionConcours;
use App\Entity\ReponseCandidat;
use App\Entity\Session;
use App\Repository\NiveauScolaireRepository;
use DateTime;

class ReponsesCandidatImport
{

    public function __construct(
        private readonly NiveauScolaireRepository $niveauScolaireRepository,
    )
    {
    }

    /**
     * @param Session $session
     * @param array $rawReponses
     * @return array
     */
    public function import(Session $session, array $rawReponses): array
    {
        $reponses = [];


        $questions = $session->concours->questions;

        foreach ($rawReponses as $rawReponse) {


            $questionsReponses = [];

            /** @var QuestionConcours $question */
            foreach ($questions as $question) {
                $reponseQuestionString = $rawReponse[ReponsesCandidatExport::questionColumnName($question->indice)];
                $questionsReponses[$question->indice] = ReponsesCandidatExport::REPONSE_VALUE_TO_INDEX[$reponseQuestionString];
            }

            $reponses[] = new ReponseCandidat(
                id: 0,
                session: $session,
                reponses: $questionsReponses,
                nom: $rawReponse[ReponsesCandidatExport::NOM_KEY],
                prenom: $rawReponse[ReponsesCandidatExport::PRENOM_KEY],
                nom_jeune_fille: $rawReponse[ReponsesCandidatExport::NOM_JEUNE_FILLE_KEY],
                niveau_scolaire: $this->niveauScolaireRepository->findOneBy(["nom" => $rawReponse[ReponsesCandidatExport::NIVEAU_SCOLAIRE_KEY]]),
                date_de_naissance: DateTime::createFromFormat(ReponsesCandidatExport::DATE_FORMAT, $rawReponse[ReponsesCandidatExport::DATE_DE_NAISSANCE_KEY]),
                sexe: ReponsesCandidatExport::VALUE_TO_INDEX_SEXE[$rawReponse[ReponsesCandidatExport::SEXE_KEY]],
                reserve: $rawReponse[ReponsesCandidatExport::RESERVE_KEY],
                autre_1: $rawReponse[ReponsesCandidatExport::AUTRE_1_KEY],
                autre_2: $rawReponse[ReponsesCandidatExport::AUTRE_2_KEY],
                code_barre: $rawReponse[ReponsesCandidatExport::CODE_BARRE_KEY],
                eirs: $rawReponse[ReponsesCandidatExport::EIRS_KEY],
                raw: null
            );
        }

        return $reponses;
    }
}