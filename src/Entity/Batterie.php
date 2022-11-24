<?php

namespace App\Entity;

use App\Repository\BatterieRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: BatterieRepository::class)]
class Batterie implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private string $grille;

    /**
     * Référence d'un livret de questions
     */
    #[ORM\Column]
    private string $batterie;

    /**
     * @param int $id
     * @param string $grille
     * @param string $batterie
     */
    public function __construct(int $id, string $grille, string $batterie)
    {
        $this->id = $id;
        $this->grille = $grille;
        $this->batterie = $batterie;
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
     * @return string
     */
    public function getGrille(): string
    {
        return $this->grille;
    }

    /**
     * @param string $grille
     */
    public function setGrille(string $grille): void
    {
        $this->grille = $grille;
    }

    /**
     * @return string
     */
    public function getBatterie(): string
    {
        return $this->batterie;
    }

    /**
     * @param string $batterie
     */
    public function setBatterie(string $batterie): void
    {
        $this->batterie = $batterie;
    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
