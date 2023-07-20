<?php

namespace App\Form\Data;

use App\Entity\NiveauScolaire;
use App\Entity\Session;
use DateTime;

class RechercheParameters
{
    const PAGE_SIZE = 20;

    public string $filtrePrenom;
    public string $filtreNom;
    public int $page;
    public DateTime $filtreDateDeNaissanceMin;
    public DateTime $filtreDateDeNaissanceMax;
    public ?NiveauScolaire $niveauScolaire;
    public ?Session $session;
    /**
     * @var ReponseCandidatChecked[]
     */
    public array $checkedReponsesCandidat;

    /**
     * @param string $filtrePrenom
     * @param string $filtreNom
     * @param int $page
     * @param DateTime $filtreDateDeNaissanceMin
     * @param DateTime $filtreDateDeNaissanceMax
     * @param NiveauScolaire|null $niveauScolaire
     * @param Session|null $session
     * @param ReponseCandidatChecked[] $checkedReponsesCandidat
     */
    public function __construct(string $filtrePrenom, string $filtreNom, int $page, DateTime $filtreDateDeNaissanceMin, DateTime $filtreDateDeNaissanceMax, ?NiveauScolaire $niveauScolaire, ?Session $session, array $checkedReponsesCandidat)
    {
        $this->filtrePrenom = $filtrePrenom;
        $this->filtreNom = $filtreNom;
        $this->page = $page;
        $this->filtreDateDeNaissanceMin = $filtreDateDeNaissanceMin;
        $this->filtreDateDeNaissanceMax = $filtreDateDeNaissanceMax;
        $this->niveauScolaire = $niveauScolaire;
        $this->session = $session;
        $this->checkedReponsesCandidat = $checkedReponsesCandidat;
    }


}