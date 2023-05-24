<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    public DateTime $date;

    /**
     * @var int L'indice de cette session
     * Sert à générer des numéros de session [type_concours]_[annee]_[numero_ordre]
     */
    #[PositiveOrZero]
    #[ORM\Column]
    public int $numero_ordre;

    #[ORM\Column(length: 2048)]
    public string $observations;

    #[ORM\ManyToOne(targetEntity: Concours::class, inversedBy: "sessions")]
    public Concours $concours;

    #[ORM\ManyToOne(targetEntity: Sgap::class, inversedBy: "sessions")]
    public Sgap $sgap;

    #[ORM\OneToMany(mappedBy: "session", targetEntity: ReponseCandidat::class, cascade: ["remove", "persist"], fetch: 'EAGER')]
    public Collection $reponses_candidats;

    /**
     * @param int $id
     * @param DateTime $date
     * @param int $numero_ordre
     * @param string $observations
     * @param Concours $concours
     * @param Sgap $sgap
     * @param Collection $reponses_candidats
     */
    public function __construct(int $id, DateTime $date, int $numero_ordre, string $observations, Concours $concours, Sgap $sgap, Collection $reponses_candidats)
    {
        $this->id = $id;
        $this->date = $date;
        $this->numero_ordre = $numero_ordre;
        $this->observations = $observations;
        $this->concours = $concours;
        $this->sgap = $sgap;
        $this->reponses_candidats = $reponses_candidats;
    }
}
