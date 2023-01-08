<?php

namespace App\Entity;

use App\Repository\DefinitionProfilComputerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DefinitionProfilComputerRepository::class)]
class DefinitionProfilComputer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\ManyToOne(targetEntity: DefinitionScore::class)]
    public DefinitionScore $score;

    #[ORM\Column]
    public string $nom;

    #[ORM\Column]
    public string $nom_php;

    /**
     * @param int $id
     * @param DefinitionScore $score
     * @param string $nom
     * @param string $nom_php
     */
    public function __construct(int $id, DefinitionScore $score, string $nom, string $nom_php)
    {
        $this->id = $id;
        $this->score = $score;
        $this->nom = $nom;
        $this->nom_php = $nom_php;
    }


}
