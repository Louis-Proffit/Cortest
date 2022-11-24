<?php

namespace App\Entity;

use App\Repository\EpreuveCandidatRepository;
use App\Repository\EpreuveRepository;
use App\Repository\ProfilCandidatRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfilCandidatRepository::class)]
class ProfilCandidat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(targetEntity: EpreuveCandidat::class)]
    private EpreuveCandidat $candidat;

    #[ORM\Column(type: Types::JSON)]
    private string $profil;

    /**
     * @param int $id
     * @param EpreuveCandidat $candidat
     * @param string $profil
     */
    public function __construct(int $id, EpreuveCandidat $candidat, string $profil)
    {
        $this->id = $id;
        $this->candidat = $candidat;
        $this->profil = $profil;
    }


}
