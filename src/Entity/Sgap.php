<?php

namespace App\Entity;

use App\Repository\SgapRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

#[ORM\Entity(repositoryClass: SgapRepository::class)]
class Sgap
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[PositiveOrZero]
    #[ORM\Column(unique: true)]
    public int $index;

    #[NotBlank]
    #[ORM\Column(unique: true)]
    public string $nom;

    /**
     * @param int $id
     * @param int $index
     * @param string $nom
     */
    public function __construct(int $id, int $index, string $nom)
    {
        $this->id = $id;
        $this->index = $index;
        $this->nom = $nom;
    }
}