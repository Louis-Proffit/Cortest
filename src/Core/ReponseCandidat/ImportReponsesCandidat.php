<?php

namespace App\Core\ReponseCandidat;

use App\Core\Exception\ImportReponsesCandidatException;
use App\Entity\QuestionTest;
use App\Entity\ReponseCandidat;
use App\Entity\Session;
use App\Entity\Test;

readonly class ImportReponsesCandidat
{

    public function __construct(
        private ImportReponseCandidat $importReponseCandidat
    )
    {
    }

    /**
     * @param Session $session
     * @param string[][] $rawReponsesCandidat
     * @return ReponseCandidat[]
     * @throws ImportReponsesCandidatException si une colonne est manquante dans les réponses brutes. Le test est effectué pour chaque ligne
     */
    public function import(Session $session, array $rawReponsesCandidat): array
    {
        $reponses = [];

        $questions = $session->test->questions->toArray();
        $requiredKeys = $this->requiredKeys($session->test);

        foreach ($rawReponsesCandidat as $rawReponseCandidat) {

            foreach (array_diff($requiredKeys, array_keys($rawReponseCandidat)) as $diff) {
                throw new ImportReponsesCandidatException("La colonne $diff est manquante dans le fichier importé. Veuillez l'ajouter avec des valeurs acceptables.");
            }

            $reponses[] = $this->importReponseCandidat->importReponse(
                session: $session,
                questions: $questions,
                rawReponsesCandidat: $rawReponseCandidat
            );
        }

        return $reponses;
    }

    /**
     * Renvoie la liste des noms des colonnes attendues pour le concours.
     * Cela dépend du concours et non de la session.
     * @param Test $test
     * @return array
     */
    private function requiredKeys(Test $test): array
    {
        $keys = [
            ExportReponseCandidat::NOM_KEY,
            ExportReponseCandidat::PRENOM_KEY,
            ExportReponseCandidat::NOM_JEUNE_FILLE_KEY,
            ExportReponseCandidat::NIVEAU_SCOLAIRE_KEY,
            ExportReponseCandidat::DATE_DE_NAISSANCE_KEY,
            ExportReponseCandidat::SEXE_KEY,
            ExportReponseCandidat::RESERVE_KEY,
            ExportReponseCandidat::AUTRE_1_KEY,
            ExportReponseCandidat::AUTRE_2_KEY,
            ExportReponseCandidat::CODE_BARRE_KEY,
            ExportReponseCandidat::EIRS_KEY,
        ];

        /** @var QuestionTest $question */
        foreach ($test->questions as $question) {
            $keys[] = $question->abreviation;
        }

        return $keys;
    }
}