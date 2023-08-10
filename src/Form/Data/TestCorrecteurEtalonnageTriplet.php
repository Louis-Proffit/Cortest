<?php

namespace App\Form\Data;

use App\Entity\Correcteur;
use App\Entity\Etalonnage;
use App\Entity\Test;

class TestCorrecteurEtalonnageTriplet
{
    public Test $test;
    public Correcteur $correcteur;
    public Etalonnage $etalonnage;

    /**
     * @param Test $test
     * @param Correcteur $correcteur
     * @param Etalonnage $etalonnage
     */
    public function __construct(Test $test, Correcteur $correcteur, Etalonnage $etalonnage)
    {
        $this->test = $test;
        $this->correcteur = $correcteur;
        $this->etalonnage = $etalonnage;
    }

    public function summary(): string
    {
        return "Test : " . $this->test->nom . " | Correcteur : " . $this->correcteur->nom . " | Etalonnage : " . $this->etalonnage->nom;
    }
}