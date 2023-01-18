<?php

namespace App\Entity;

use App\Constraint\ClassName;
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

    #[ClassName]
    #[ORM\Column]
    public string $grille_class;

    #[ORM\ManyToOne(targetEntity: Sgap::class)]
    public Sgap $sgap;

    #[ORM\OneToMany(mappedBy: "session", targetEntity: ReponseCandidat::class)]
    public Collection $reponses_candidats;

    /**
     * @param int $id
     * @param DateTime $date
     * @param string $grille_class
     * @param Sgap $sgap
     * @param Collection $reponses_candidats
     */
    public function __construct(int $id, DateTime $date, string $grille_class, Sgap $sgap, Collection $reponses_candidats)
    {
        $this->id = $id;
        $this->date = $date;
        $this->grille_class = $grille_class;
        $this->sgap = $sgap;
        $this->reponses_candidats = $reponses_candidats;
    }


}
