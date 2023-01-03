<?php

namespace App\Entity;

use App\Repository\CandidatScoreRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CandidatScoreRepository::class)]
class CandidatScore
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\ManyToOne(targetEntity: CandidatReponse::class)]
    public CandidatReponse $reponses;

    #[ORM\ManyToOne(targetEntity: DefinitionScoreComputer::class)]
    public DefinitionScoreComputer $scoreComputer;

    #[ORM\Column(type: Types::JSON)]
    public string $score;
}
