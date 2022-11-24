<?php

namespace App\Entity;

use _PHPStan_582a9cb8b\Nette\Utils\Json;
use App\Repository\EpreuveCandidatRepository;
use App\Repository\EpreuveRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EpreuveCandidatRepository::class)]
class EpreuveCandidat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Session::class, inversedBy: 'candidats')]
    private Session $session;

    #[ORM\Column(type: Types::JSON, options: ['jsonb' => true])]
    private string $reponses;

    /**
     * @param int $id
     * @param Session $session
     * @param string $reponses
     */
    public function __construct(int $id, Session $session, string $reponses)
    {
        $this->id = $id;
        $this->session = $session;
        $this->reponses = $reponses;
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
     * @return Session
     */
    public function getSession(): Session
    {
        return $this->session;
    }

    /**
     * @param Session $session
     */
    public function setSession(Session $session): void
    {
        $this->session = $session;
    }

    /**
     * @return string
     */
    public function getReponses(): string
    {
        return $this->reponses;
    }

    /**
     * @param string $reponses
     */
    public function setReponses(string $reponses): void
    {
        $this->reponses = $reponses;
    }


}
