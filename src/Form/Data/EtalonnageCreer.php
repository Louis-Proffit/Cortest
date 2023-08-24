<?php

namespace App\Form\Data;

use App\Entity\Structure;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class EtalonnageCreer
{

    public Structure $structure;

    #[GreaterThanOrEqual(2, message: "Le nombre de classes doit être supérieur à deux")]
    public int $nombre_classes;

    #[NotBlank]
    public string $nom;

    /**
     * @param Structure $structure
     * @param int $nombre_classes
     * @param string $nom
     */
    public function __construct(Structure $structure, int $nombre_classes, string $nom)
    {
        $this->structure = $structure;
        $this->nombre_classes = $nombre_classes;
        $this->nom = $nom;
    }
}