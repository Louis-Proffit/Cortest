<?php

namespace App\Entity;

use App\Repository\ReponseCandidatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReponseCandidatRepository::class)]
class ReponseCandidat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\ManyToOne(targetEntity: Session::class, inversedBy: 'candidats')]
    public Session $session;

    #[ORM\Column]
    public array $reponses;

    #[ORM\Column(type: Types::JSON)]
    public array $raw;

    /**
     * @param int $id
     * @param Session $session
     * @param array $reponses
     * @param array $raw
     */
    public function __construct(int $id, Session $session, array $reponses, array $raw)
    {
        $this->id = $id;
        $this->session = $session;
        $this->reponses = $reponses;
        $this->raw = $raw;
    }
}
