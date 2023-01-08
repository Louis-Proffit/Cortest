<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    public DateTime $date;

    #[ORM\Column]
    public int $sgap_index;

    #[ORM\ManyToOne(targetEntity: DefinitionGrille::class)]
    public DefinitionGrille $grille;

    #[ORM\OneToMany(mappedBy: "session", targetEntity: CandidatReponse::class)]
    public Collection $candidats;

}
