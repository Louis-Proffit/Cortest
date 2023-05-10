<?php

namespace App\Core\Files\Csv\Reponses;

use App\Entity\Concours;
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
     * @return ReponseCandidat[]
     * @throws ReponsesCandidatImportException si une colonne est manquante dans les réponses brutes. Le test est effectué pour chaque ligne
     */
    public function import(Session $session, array $rawReponses): array
    {
        $reponses = [];

        $questions = $session->concours->questions;
        $requiredKeys = $this->requiredKeys($session->concours);

        foreach ($rawReponses as $rawReponse) {

            foreach (array_diff($requiredKeys, array_keys($rawReponse)) as $diff) {
                throw new ReponsesCandidatImportException("La colonne $diff est manquante dans le fichier importé. Veuillez l'ajouter avec des valeurs acceptables.");
            }

            $questionsReponses = [];

            /** @var QuestionConcours $question */
            foreach ($questions as $question) {
                $reponseQuestionString = $rawReponse[ReponsesCandidatExport::questionColumnName($question->indice)];
                $questionsReponses[$question->indice] = ReponsesCandidatExport::REPONSE_VALUE_TO_INDEX[$reponseQuestionString];
            }

            $nomNiveauScolaire = $rawReponse[ReponsesCandidatExport::NIVEAU_SCOLAIRE_KEY];
            $niveauScolaire = $this->niveauScolaireRepository->findOneBy(["nom" => $nomNiveauScolaire]);

            if ($niveauScolaire == null) {
                throw new ReponsesCandidatImportException("Le niveau scolaire $nomNiveauScolaire n'existe pas. Veuillez le corriger");
            }

            $sexeNom = $rawReponse[ReponsesCandidatExport::SEXE_KEY];
            if (!key_exists($sexeNom, ReponsesCandidatExport::VALUE_TO_INDEX_SEXE)) {
                throw new ReponsesCandidatImportException("Le sexe $sexeNom n'existe pas. Les valeurs possibles sont : " . implode(",", ReponsesCandidatExport::VALUE_TO_INDEX_SEXE));
            }

            $reponses[] = new ReponseCandidat(
                id: 0,
                session: $session,
                reponses: $questionsReponses,
                nom: $rawReponse[ReponsesCandidatExport::NOM_KEY],
                prenom: $rawReponse[ReponsesCandidatExport::PRENOM_KEY],
                nom_jeune_fille: $rawReponse[ReponsesCandidatExport::NOM_JEUNE_FILLE_KEY],
                niveau_scolaire: $niveauScolaire,
                date_de_naissance: DateTime::createFromFormat(ReponsesCandidatExport::DATE_FORMAT, $rawReponse[ReponsesCandidatExport::DATE_DE_NAISSANCE_KEY]),
                sexe: ReponsesCandidatExport::VALUE_TO_INDEX_SEXE[$sexeNom],
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

    /**
     * Renvoie la liste des noms des colonnes attendues pour le concours.
     * Cela dépend du concours et non de la session.
     * @param Concours $concours
     * @return array
     */
    private function requiredKeys(Concours $concours): array
    {
        $keys = [
            ReponsesCandidatExport::NOM_KEY,
            ReponsesCandidatExport::PRENOM_KEY,
            ReponsesCandidatExport::NOM_JEUNE_FILLE_KEY,
            ReponsesCandidatExport::NIVEAU_SCOLAIRE_KEY,
            ReponsesCandidatExport::DATE_DE_NAISSANCE_KEY,
            ReponsesCandidatExport::SEXE_KEY,
            ReponsesCandidatExport::RESERVE_KEY,
            ReponsesCandidatExport::AUTRE_1_KEY,
            ReponsesCandidatExport::AUTRE_2_KEY,
            ReponsesCandidatExport::CODE_BARRE_KEY,
            ReponsesCandidatExport::EIRS_KEY,
        ];

        /** @var QuestionConcours $question */
        foreach ($concours->questions as $question) {
            $keys[] = ReponsesCandidatExport::questionColumnName($question->indice);
        }

        return $keys;
    }
}