<?php

namespace App\Entity;

use App\Repository\BatterieRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: ProfilFunctionRefRepository::class)]
class ProfilFunctionRef
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Batterie::class)]
    private Batterie $batterie;

    /**
     * Référence d'un livret de questions
     */
    #[ORM\Column]
    private string $file_name;
}
