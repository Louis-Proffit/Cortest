<?php

namespace App\Entity;

use App\Repository\CandidatReponseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CandidatReponseRepository::class)]
class CandidatReponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\ManyToOne(targetEntity: Session::class, inversedBy: 'candidats')]
    public Session $session;

    #[ORM\Column(type: Types::JSON)]
    public string $reponses;

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
}
