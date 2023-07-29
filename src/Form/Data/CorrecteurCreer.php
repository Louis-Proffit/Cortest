<?php

namespace App\Form\Data;

use App\Entity\Concours;
use App\Entity\Structure;
use Symfony\Component\Validator\Constraints\NotBlank;

class CorrecteurCreer
{

    public Structure $profil;

    public Concours $concours;
    #[NotBlank]
    public string $nom;

    /**
     * @param Structure $profil
     * @param Concours $concours
     * @param string $nom
     */
    public function __construct(Structure $profil, Concours $concours, string $nom)
    {
        $this->profil = $profil;
        $this->concours = $concours;
        $this->nom = $nom;
    }


}