<?php

namespace App\Entity;

use App\Repository\EpreuveRepository;
use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private int $date;

    #[ORM\OneToMany(mappedBy: '$session', targetEntity: EpreuveCandidat::class)]
    private array $candidats;

    #[ORM\ManyToOne(targetEntity: Batterie::class)]
    private Batterie $batterie;

    /**
     * @param int $id
     * @param int $date
     * @param array $candidats
     * @param Batterie $batterie
     */
    public function __construct(int $id, int $date, array $candidats, Batterie $batterie)
    {
        $this->id = $id;
        $this->date = $date;
        $this->candidats = $candidats;
        $this->batterie = $batterie;
    }


    /**
     * @param array $candidats
     */
    public function setCandidats(array $candidats): void
    {
        $this->candidats = $candidats;
    }

    /**
     * @return array
     */
    public function getCandidats(): array
    {
        return $this->candidats;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getDate(): int
    {
        return $this->date;
    }

    /**
     * @param int $date
     */
    public function setDate(int $date): void
    {
        $this->date = $date;
    }

    /**
     * @return Batterie
     */
    public function getBatterie(): Batterie
    {
        return $this->batterie;
    }

    /**
     * @param Batterie $batterie
     */
    public function setBatterie(Batterie $batterie): void
    {
        $this->batterie = $batterie;
    }


}
