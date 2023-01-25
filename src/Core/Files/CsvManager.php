<?php

namespace App\Core\Files;

use App\Entity\Echelle;
use App\Entity\Profil;
use App\Entity\ReponseCandidat;
use App\Entity\Session;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

class CsvManager
{
    const CSV_TMP_FILE_NAME = "tmp/session.csv";

    protected function scoreFileName(Session $session): string
    {
        return "scores_session_" . $session->date->format("d-m-Y") . "_" . $session->concours->nom . ".csv";
    }

    protected function profilFileName(Session $session): string
    {
        return "profils_session_" . $session->date->format("d-m-Y") . "_" . $session->concours->nom . ".csv";
    }

    public function exportProfils(Session $session, Profil $profil, array $profils): BinaryFileResponse
    {
        $encoder = new CsvEncoder();

        $data = [];

        /** @var ReponseCandidat $reponse */
        foreach ($session->reponses_candidats as $reponse) {
            $toAdd = [
                "Nom" => $reponse->nom,
                "Prenom" => $reponse->prenom,
                "Nom de jeune fille" => $reponse->nom_jeune_fille,
                "Date de naissance" => $reponse->date_de_naissance->format("d/m/Y"),
            ];

            $candidat_profil = $profils[$reponse->id];
            /** @var Echelle $echelle */
            foreach ($profil->echelles as $echelle) {
                $toAdd[$echelle->nom] = $candidat_profil[$echelle->nom_php];
            }

            $data[] = $toAdd;
        }
        $encoded = $encoder->encode($data, CsvEncoder::FORMAT);

        $tmp = fopen(self::CSV_TMP_FILE_NAME, 'w+');
        fwrite($tmp, $encoded);
        fclose($tmp);

        $result = new BinaryFileResponse(self::CSV_TMP_FILE_NAME);
        $result->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $this->profilFileName($session)
        );

        return $result;
    }

    public function exportScores(Session $session, Profil $profil, array $scores): BinaryFileResponse
    {
        $encoder = new CsvEncoder();

        $data = [];

        /** @var ReponseCandidat $reponse */
        foreach ($session->reponses_candidats as $reponse) {
            $toAdd = [
                "Nom" => $reponse->nom,
                "Prenom" => $reponse->prenom,
                "Nom de jeune fille" => $reponse->nom_jeune_fille,
                "Date de naissance" => $reponse->date_de_naissance->format("d/m/Y"),
            ];

            $candidat_scores = $scores[$reponse->id];
            /** @var Echelle $echelle */
            foreach ($profil->echelles as $echelle) {
                $toAdd[$echelle->nom] = $candidat_scores[$echelle->nom_php];
            }

            $data[] = $toAdd;
        }
        $encoded = $encoder->encode($data, CsvEncoder::FORMAT);

        $tmp = fopen(self::CSV_TMP_FILE_NAME, 'w+');
        fwrite($tmp, $encoded);
        fclose($tmp);

        $result = new BinaryFileResponse(self::CSV_TMP_FILE_NAME);
        $result->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $this->scoreFileName($session)
        );

        return $result;
    }

    /**
     * @param ReponseCandidat[] $reponses
     * @param string $file_name
     * @return BinaryFileResponse
     */
    public function exportReponses(array $reponses, string $file_name): BinaryFileResponse
    {
        $encoder = new CsvEncoder();

        $data = [];

        foreach ($reponses as $reponse) {
            $toAdd = [
                "Nom" => $reponse->nom,
                "Prenom" => $reponse->prenom,
                "Nom de jeune fille" => $reponse->nom_jeune_fille,
                "Niveau scolaire" => $reponse->niveau_scolaire->nom,
                "Date de naissance" => $reponse->date_de_naissance->format("d/m/Y"),
                "Sexe" => match ($reponse->sexe) {
                    ReponseCandidat::INDEX_HOMME => "Homme",
                    default => "Femme"
                },
                "Réservé" => $reponse->reserve,
                "Autre 1" => $reponse->autre_1,
                "Autre 2" => $reponse->autre_2,
                "Code barre" => $reponse->code_barre,
            ];

            $index = 1;
            foreach ($reponse->reponses as $value) {
                $toAdd["Réponse " . $index] = $value;
                $index++;
            }

            $data[] = $toAdd;
        }
        $encoded = $encoder->encode($data, CsvEncoder::FORMAT);

        $tmp = fopen(self::CSV_TMP_FILE_NAME, 'w+');
        fwrite($tmp, $encoded);
        fclose($tmp);

        $result = new BinaryFileResponse(self::CSV_TMP_FILE_NAME);
        $result->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $file_name
        );

        return $result;
    }
}