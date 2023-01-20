<?php

namespace App\Entity;

use App\Constraint\ClassName;
use App\Repository\SessionRepository;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\LessThan;
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

    #[ClassName]
    #[ORM\Column]
    public string $grille_class;

    /**
     * @var int L'indice de cette session
     * Sert à générer des numéros de session [type_concours]_[annee]_[numero_ordre]
     */
    #[PositiveOrZero]
    #[ORM\Column]
    public int $numero_ordre;

    #[ORM\Column(length: 2048)]
    public string $observations;

    #[ORM\ManyToOne(targetEntity: Concours::class)]
    public Concours $concours;

    #[LessThan(value: 100)]
    #[PositiveOrZero]
    #[ORM\Column]
    public int $type_concours;

    #[LessThan(value: 1000)]
    #[PositiveOrZero]
    #[ORM\Column]
    public int $version_batterie;

    #[ORM\ManyToOne(targetEntity: Sgap::class)]
    public Sgap $sgap;

    #[ORM\OneToMany(mappedBy: "session", targetEntity: ReponseCandidat::class)]
    public Collection $reponses_candidats;

    /**
     * @param int $id
     * @param DateTime $date
     * @param string $grille_class
     * @param int $numero_ordre
     * @param string $observations
     * @param Concours $concours
     * @param int $type_concours
     * @param int $version_batterie
     * @param Sgap $sgap
     * @param Collection $reponses_candidats
     */
    public function __construct(int $id, DateTime $date, string $grille_class, int $numero_ordre, string $observations, Concours $concours, int $type_concours, int $version_batterie, Sgap $sgap, Collection $reponses_candidats)
    {
        $this->id = $id;
        $this->date = $date;
        $this->grille_class = $grille_class;
        $this->numero_ordre = $numero_ordre;
        $this->observations = $observations;
        $this->concours = $concours;
        $this->type_concours = $type_concours;
        $this->version_batterie = $version_batterie;
        $this->sgap = $sgap;
        $this->reponses_candidats = $reponses_candidats;
    }


}
