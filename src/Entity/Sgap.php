<?php

namespace App\Entity;

use App\Repository\SgapRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SgapRepository::class)]
class Sgap
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;


    #[ORM\Column]
    public int $index;

    #[ORM\Column]
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