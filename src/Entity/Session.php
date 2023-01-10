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

    #[ORM\Column]
    public string $grilleClass;

    #[ORM\OneToMany(mappedBy: "session", targetEntity: CandidatReponse::class)]
    public Collection $reponses_candidats;

    /**
     * @param int $id
     * @param DateTime $date
     * @param int $sgap_index
     * @param string $grilleClass
     * @param Collection $reponses_candidats
     */
    public function __construct(int $id, DateTime $date, int $sgap_index, string $grilleClass, Collection $reponses_candidats)
    {
        $this->id = $id;
        $this->date = $date;
        $this->sgap_index = $sgap_index;
        $this->grilleClass = $grilleClass;
        $this->reponses_candidats = $reponses_candidats;
    }


}
