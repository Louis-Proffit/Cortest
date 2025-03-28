<?php

namespace App\Entity;

use App\Form\ParametresLectureFichierType;
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

    #[ORM\ManyToOne(targetEntity: Test::class, inversedBy: "sessions")]
    public Test $test;

    #[ORM\ManyToOne(targetEntity: Concours::class, inversedBy: "sessions")]
    public Concours $concours;

    #[ORM\ManyToOne(targetEntity: Sgap::class, inversedBy: "sessions")]
    public Sgap $sgap;

    #[ORM\OneToMany(mappedBy: "session", targetEntity: ReponseCandidat::class, cascade: ["remove", "persist"], fetch: 'EAGER')]
    #[ORM\OrderBy(["nom" => "ASC", "prenom" => "ASC"])]
    public Collection $reponses_candidats;

    /**
     * @param int $id
     * @param DateTime $date
     * @param int $numero_ordre
     * @param string $observations
     * @param Test $test
     * @param Sgap $sgap
     * @param Collection $reponses_candidats
     */
    public function __construct(int $id, DateTime $date, int $numero_ordre, string $observations, Test $test, Sgap $sgap, Collection $reponses_candidats, Concours $concours)
    {
        $this->id = $id;
        $this->date = $date;
        $this->numero_ordre = $numero_ordre;
        $this->observations = $observations;
        $this->test = $test;
        $this->sgap = $sgap;
        $this->reponses_candidats = $reponses_candidats;
        $this->concours = $concours;
    }

    public static function formatLong(Session $session): string
    {
        return $session->date->format("d-m-Y") . " | " . Sgap::affichage($session->sgap) . " | " . $session->concours->intitule ;
    }

    public function sessionDisplay(): string
    {
        return "Session : "
            . $this->date->format('Y-m-d')
            . " | "
            . $this->sgap->nom
            . " | "
            . $this->test->nom;
    }
}
