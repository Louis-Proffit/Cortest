<?php

namespace App\Form\Data;

use App\Entity\Profil;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class EtalonnageCreer
{

    public Profil $profil;

    #[GreaterThanOrEqual(2, message: "Le nombre de classes doit être supérieur à deux")]
    public int $nombre_classes;

    #[NotBlank]
    public string $nom;

    /**
     * @param Profil $profil
     * @param int $nombre_classes
     * @param string $nom
     */
    public function __construct(Profil $profil, int $nombre_classes, string $nom)
    {
        $this->profil = $profil;
        $this->nombre_classes = $nombre_classes;
        $this->nom = $nom;
    }
}