<?php

namespace App\Form\Data;

use App\Constraint\ClassName;
use App\Entity\Concours;
use App\Entity\Profil;
use Symfony\Component\Validator\Constraints\NotBlank;

class CorrecteurCreer
{

    public Profil $profil;

    public Concours $concours;
    #[NotBlank]
    public string $nom;

    /**
     * @param Profil $profil
     * @param Concours $concours
     * @param string $nom
     */
    public function __construct(Profil $profil, Concours $concours, string $nom)
    {
        $this->profil = $profil;
        $this->concours = $concours;
        $this->nom = $nom;
    }


}