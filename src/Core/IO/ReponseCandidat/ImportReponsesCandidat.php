<?php

namespace App\Core\IO\ReponseCandidat;

use App\Entity\Concours;
use App\Entity\QuestionConcours;
use App\Entity\ReponseCandidat;
use App\Entity\Session;
use App\Repository\NiveauScolaireRepository;
use DateTime;

class ImportReponsesCandidat
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
     * @throws ImportReponsesCandidatException si une colonne est manquante dans les réponses brutes. Le test est effectué pour chaque ligne
     */
    public function import(Session $session, array $rawReponses): array
    {
        $reponses = [];

        $questions = $session->concours->questions;
        $requiredKeys = $this->requiredKeys($session->concours);

        foreach ($rawReponses as $rawReponse) {

            foreach (array_diff($requiredKeys, array_keys($rawReponse)) as $diff) {
                throw new ImportReponsesCandidatException("La colonne $diff est manquante dans le fichier importé. Veuillez l'ajouter avec des valeurs acceptables.");
            }

            $questionsReponses = [];

            /** @var QuestionConcours $question */
            foreach ($questions as $question) {
                $reponseQuestionString = $rawReponse[$question->abreviation];
                $questionsReponses[$question->indice] = ExportReponsesCandidat::REPONSE_VALUE_TO_INDEX[$reponseQuestionString];
            }

            $nomNiveauScolaire = $rawReponse[ExportReponsesCandidat::NIVEAU_SCOLAIRE_KEY];
            $niveauScolaire = $this->niveauScolaireRepository->findOneBy(["nom" => $nomNiveauScolaire]);

            if ($niveauScolaire == null) {
                throw new ImportReponsesCandidatException("Le niveau scolaire $nomNiveauScolaire n'existe pas. Veuillez le corriger");
            }

            $sexeNom = $rawReponse[ExportReponsesCandidat::SEXE_KEY];
            if (!key_exists($sexeNom, ExportReponsesCandidat::VALUE_TO_INDEX_SEXE)) {
                throw new ImportReponsesCandidatException("Le sexe $sexeNom n'existe pas. Les valeurs possibles sont : " . implode(",", ExportReponsesCandidat::VALUE_TO_INDEX_SEXE));
            }

            $reponses[] = new ReponseCandidat(
                id: 0,
                session: $session,
                reponses: $questionsReponses,
                nom: $rawReponse[ExportReponsesCandidat::NOM_KEY],
                prenom: $rawReponse[ExportReponsesCandidat::PRENOM_KEY],
                nom_jeune_fille: $rawReponse[ExportReponsesCandidat::NOM_JEUNE_FILLE_KEY],
                niveau_scolaire: $niveauScolaire,
                date_de_naissance: DateTime::createFromFormat(ExportReponsesCandidat::DATE_FORMAT, $rawReponse[ExportReponsesCandidat::DATE_DE_NAISSANCE_KEY]),
                sexe: ExportReponsesCandidat::VALUE_TO_INDEX_SEXE[$sexeNom],
                reserve: $rawReponse[ExportReponsesCandidat::RESERVE_KEY],
                autre_1: $rawReponse[ExportReponsesCandidat::AUTRE_1_KEY],
                autre_2: $rawReponse[ExportReponsesCandidat::AUTRE_2_KEY],
                code_barre: $rawReponse[ExportReponsesCandidat::CODE_BARRE_KEY],
                eirs: $rawReponse[ExportReponsesCandidat::EIRS_KEY],
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
            ExportReponsesCandidat::NOM_KEY,
            ExportReponsesCandidat::PRENOM_KEY,
            ExportReponsesCandidat::NOM_JEUNE_FILLE_KEY,
            ExportReponsesCandidat::NIVEAU_SCOLAIRE_KEY,
            ExportReponsesCandidat::DATE_DE_NAISSANCE_KEY,
            ExportReponsesCandidat::SEXE_KEY,
            ExportReponsesCandidat::RESERVE_KEY,
            ExportReponsesCandidat::AUTRE_1_KEY,
            ExportReponsesCandidat::AUTRE_2_KEY,
            ExportReponsesCandidat::CODE_BARRE_KEY,
            ExportReponsesCandidat::EIRS_KEY,
        ];

        /** @var QuestionConcours $question */
        foreach ($concours->questions as $question) {
            $keys[] = $question->abreviation;
        }

        return $keys;
    }
}