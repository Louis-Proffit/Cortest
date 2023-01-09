<?php

namespace App\Entity;

use App\Repository\EtalonnageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtalonnageRepository::class)]
class Etalonnage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column]
    public int $score_id;

    #[ORM\Column]
    public string $nom;

    #[ORM\Column]
    public int $nombre_classes;

    #[ORM\Column(type: Types::JSON)]
    public array $values;

    /**
     * @param int $id
     * @param int $score_id
     * @param string $nom
     * @param int $nombre_classes
     * @param array $values
     */
    public function __construct(int $id, int $score_id, string $nom, int $nombre_classes, array $values)
    {
        $this->id = $id;
        $this->score_id = $score_id;
        $this->nom = $nom;
        $this->nombre_classes = $nombre_classes;
        $this->values = $values;
    }
}
