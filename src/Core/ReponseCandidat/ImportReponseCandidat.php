<?php

namespace App\Core\ReponseCandidat;

use App\Core\Exception\ImportReponsesCandidatException;
use App\Entity\QuestionTest;
use App\Entity\ReponseCandidat;
use App\Entity\Session;
use App\Repository\NiveauScolaireRepository;
use DateTime;

readonly class ImportReponseCandidat
{

    public function __construct(
        private NiveauScolaireRepository $niveauScolaireRepository,
    )
    {
    }

    /**
     * @throws ImportReponsesCandidatException
     */
    public function importReponse(Session $session, array $questions, array $rawReponsesCandidat): ReponseCandidat
    {
        $questionsReponses = [];

        /** @var QuestionTest $question */
        foreach ($questions as $question) {
            $reponseQuestionString = $rawReponsesCandidat[$question->abreviation];
            $questionsReponses[$question->indice] = ExportReponseCandidat::REPONSE_VALUE_TO_INDEX[$reponseQuestionString];
        }

        $nomNiveauScolaire = $rawReponsesCandidat[ExportReponseCandidat::NIVEAU_SCOLAIRE_KEY];
        $niveauScolaire = $this->niveauScolaireRepository->findOneBy(["nom" => $nomNiveauScolaire]);

        if ($niveauScolaire == null) {
            throw new ImportReponsesCandidatException("Le niveau scolaire $nomNiveauScolaire n'existe pas. Veuillez le corriger");
        }

        $sexeNom = $rawReponsesCandidat[ExportReponseCandidat::SEXE_KEY];
        if (!key_exists($sexeNom, ExportReponseCandidat::VALUE_TO_INDEX_SEXE)) {
            throw new ImportReponsesCandidatException("Le sexe $sexeNom n'existe pas. Les valeurs possibles sont : " . implode(",", ExportReponseCandidat::VALUE_TO_INDEX_SEXE));
        }

        return new ReponseCandidat(
            id: 0,
            session: $session,
            reponses: $questionsReponses,
            nom: $rawReponsesCandidat[ExportReponseCandidat::NOM_KEY],
            prenom: $rawReponsesCandidat[ExportReponseCandidat::PRENOM_KEY],
            nom_jeune_fille: $rawReponsesCandidat[ExportReponseCandidat::NOM_JEUNE_FILLE_KEY],
            niveau_scolaire: $niveauScolaire,
            date_de_naissance: DateTime::createFromFormat(ExportReponseCandidat::DATE_FORMAT, $rawReponsesCandidat[ExportReponseCandidat::DATE_DE_NAISSANCE_KEY]),
            sexe: ExportReponseCandidat::VALUE_TO_INDEX_SEXE[$sexeNom],
            reserve: $rawReponsesCandidat[ExportReponseCandidat::RESERVE_KEY],
            autre_1: $rawReponsesCandidat[ExportReponseCandidat::AUTRE_1_KEY],
            autre_2: $rawReponsesCandidat[ExportReponseCandidat::AUTRE_2_KEY],
            code_barre: $rawReponsesCandidat[ExportReponseCandidat::CODE_BARRE_KEY],
            eirs: $rawReponsesCandidat[ExportReponseCandidat::EIRS_KEY],
            raw: null
        );
    }
}