<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;

#[Entity]
class Graphique
{


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column]
    public array $options;

    #[ORM\ManyToOne(targetEntity: EchelleGraphique::class)]
    public Collection $echelles;

    // #[ClassName]
    #[ORM\Column]
    public string $display_class;

    /**
     * @param int $id
     * @param array $options
     * @param Collection $echelles
     * @param string $display_class
     */
    public function __construct(int $id, array $options, Collection $echelles, string $display_class)
    {
        $this->id = $id;
        $this->options = $options;
        $this->echelles = $echelles;
        $this->display_class = $display_class;
    }


}