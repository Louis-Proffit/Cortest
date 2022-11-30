<?php

namespace App\Entity;

use App\Repository\CandidatReponseRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CandidatReponseRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    public DateTime $date_saisie;

    // TODO other fields, indice de batterie par exemple

    #[ORM\ManyToOne(targetEntity: DefinitionGrille::class)]
    public DefinitionGrille $grille;

}
