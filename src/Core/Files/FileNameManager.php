<?php

namespace App\Core\Files;

use App\Entity\ReponseCandidat;
use App\Entity\Session;
use DateTime;

/**
 * Fonctions pour nommer les fichiers produits par l'application Cortest
 */
class FileNameManager
{

    const CSV_EXTENSION = ".csv";
    const ZIP_EXTENSION = ".zip";
    const PDF_EXTENSION = ".pdf";
    const DATE_FORMAT = "d-m-Y";

    /**
     * Nom d'un fichier d'export des scores d'une session au format csv
     * @param Session $session
     * @return string
     */
    public function sessionScoreCsvFileName(Session $session): string
    {
        return "scores_session_" . $this->formatDate($session->date) . "_" . $session->concours->nom . self::CSV_EXTENSION;
    }

    /**
     * Nom d'un fichier d'export des scores d'une session au format csv
     * @param Session $session
     * @return string
     */
    public function sessionProfilCsvFileName(Session $session): string
    {
        return "profils_session_" . $this->formatDate($session->date) . "_" . $session->concours->nom . self::CSV_EXTENSION;
    }

    /**
     * @param Session $session
     * @return string
     */
    public function mergedProfilsPdfFileName(Session $session): string
    {
        return "tous_profils_session_" . $this->formatDate($session->date) . self::PDF_EXTENSION;
    }

    /**
     * Détermine le nom du fichier pdf du profil d'un seul candidat
     * @param ReponseCandidat $reponseCandidat
     * @return string
     */
    public function singlePdfFileName(ReponseCandidat $reponseCandidat): string
    {
        return str_replace(" ", "_", "profil_" . $reponseCandidat->nom . "_" . $reponseCandidat->prenom . "_" . $this->formatDate($reponseCandidat->date_de_naissance));
    }

    /**
     * @param Session $session
     * @return string
     */
    public function mergedProfilsZipFileName(Session $session): string
    {
        return "tous_profils_session_" . $this->formatDate($session->date) . self::ZIP_EXTENSION;
    }


    private function formatDate(DateTime $dateTime): string
    {
        return $dateTime->format(self::DATE_FORMAT);
    }

}