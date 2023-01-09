<?php

namespace App\Entity;

use App\Repository\CorrecteurRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CorrecteurRepository::class)]
class Correcteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column]
    public int $grille_id;

    #[ORM\Column]
    public int $score_id;

    #[ORM\Column]
    public string $nom;

    #[ORM\Column(type: Types::JSON)]
    public array $values;

    /**
     * @param int $id
     * @param int $grille_id
     * @param int $score_id
     * @param string $nom
     * @param array $values
     */
    public function __construct(int $id, int $grille_id, int $score_id, string $nom, array $values)
    {
        $this->id = $id;
        $this->grille_id = $grille_id;
        $this->score_id = $score_id;
        $this->nom = $nom;
        $this->values = $values;
    }


}
