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
     * @param ReponseCandidat[] $reponsesCandidat
     * @return string
     */
    public function mergedProfilsPdfFileName(array $reponsesCandidat): string
    {
        $singleSession = $this->singleSession($reponsesCandidat);

        if (!$singleSession) {
            // Tous les profils appartiennent à la même session, l'utiliser dans le nom du fichier
            return "tous_profils_session_" . $this->formatDate($singleSession->date) . self::PDF_EXTENSION;
        }

        // Nom par défaut, impossible de faire mieux.
        return "tous_profils" . self::PDF_EXTENSION;
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
     * @param ReponseCandidat[] $reponsesCandidat
     * @return string
     */
    public function mergedProfilsZipFileName(array $reponsesCandidat): string
    {
        $singleSession = $this->singleSession($reponsesCandidat);

        if (!$singleSession) {
            // Tous les profils appartiennent à la même session, l'utiliser dans le nom du fichier
            return "tous_profils_session_" . $this->formatDate($singleSession->date) . self::ZIP_EXTENSION;
        }

        // Nom par défaut, impossible de faire mieux.
        return "tous_profils" . self::ZIP_EXTENSION;
    }

    private function singleSession(array $reponsesCandidat): Session|false
    {
        $sessions_by_id = [];

        foreach ($reponsesCandidat as $reponseCandidat) {
            $sessions_by_id[$reponseCandidat->session->id] = $reponseCandidat->session;
        }

        # reset a exactement le comportement attendu
        return reset($sessions_by_id);
    }

    private function formatDate(DateTime $dateTime): string
    {
        return $dateTime->format(self::DATE_FORMAT);
    }

}