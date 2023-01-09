<?php

namespace App\Form\Data;

use App\Entity\Correcteur;
use App\Entity\Etalonnage;

class CorrecteurEtEtalonnagePair
{
    public Correcteur $correcteur;
    public Etalonnage $etalonnage;

    /**
     * @param Correcteur $correcteur
     * @param Etalonnage $etalonnage
     */
    public function __construct(Correcteur $correcteur, Etalonnage $etalonnage)
    {
        $this->correcteur = $correcteur;
        $this->etalonnage = $etalonnage;
    }


}