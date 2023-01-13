<?php

namespace App\Form\Data;

use App\Entity\Profil;
use Symfony\Component\Validator\Constraints\NotBlank;

class CorrecteurCreer
{

    public Profil $profil;
    // #[ClassName]
    public string $grille_class;
    #[NotBlank]
    public string $nom;

    /**
     * @param Profil $profil
     * @param string $grille_class
     * @param string $nom
     */
    public function __construct(Profil $profil, string $grille_class, string $nom)
    {
        $this->profil = $profil;
        $this->grille_class = $grille_class;
        $this->nom = $nom;
    }


}