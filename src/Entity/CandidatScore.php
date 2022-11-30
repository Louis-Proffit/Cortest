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

    /**
     * @param int $id
     * @param CandidatReponse $reponses
     * @param DefinitionScoreComputer $computer
     * @param string $score
     */
    public function __construct(int $id, CandidatReponse $reponses, DefinitionScoreComputer $computer, string $score)
    {
        $this->id = $id;
        $this->reponses = $reponses;
        $this->scoreComputer = $computer;
        $this->score = $score;
    }


}
