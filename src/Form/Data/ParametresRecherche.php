<?php

namespace App\Form\Data;

use App\Entity\NiveauScolaire;
use App\Entity\Session;
use DateTime;

class ParametresRecherche
{
    const PAGE_SIZE = 100;

    public string $filtrePrenom;
    public string $filtreNom;
    public int $page;
    public ?DateTime $filtreDateDeNaissanceMin;
    public ?DateTime $filtreDateDeNaissanceMax;
    public ?DateTime $dateSession;
    public ?NiveauScolaire $niveauScolaire;
    public ?Session $session;

    /**
     * @param string $filtrePrenom
     * @param string $filtreNom
     * @param int $page
     * @param DateTime $filtreDateDeNaissanceMin
     * @param DateTime $filtreDateDeNaissanceMax
     * @param DateTime|null $dateSession
     * @param NiveauScolaire|null $niveauScolaire
     * @param Session|null $session
     */
    public function __construct(string $filtrePrenom, string $filtreNom, int $page, ?DateTime $filtreDateDeNaissanceMin, ?DateTime $filtreDateDeNaissanceMax, ?DateTime $dateSession, ?NiveauScolaire $niveauScolaire, ?Session $session)
    {
        $this->filtrePrenom = $filtrePrenom;
        $this->filtreNom = $filtreNom;
        $this->page = $page;
        $this->filtreDateDeNaissanceMin = $filtreDateDeNaissanceMin;
        $this->filtreDateDeNaissanceMax = $filtreDateDeNaissanceMax;
        $this->dateSession = $dateSession;
        $this->niveauScolaire = $niveauScolaire;
        $this->session = $session;
    }


}