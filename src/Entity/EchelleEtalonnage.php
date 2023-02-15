<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\PseudoTypes\Numeric_;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Expression;
use Symfony\Component\Validator\Constraints\Type;

#[Entity]
class EchelleEtalonnage
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[Expression("this.etalonnage.nombre_classes == count(this.bounds)")]
    #[All(new Type("numeric"))]
    #[ORM\Column]
    public array $bounds;

    #[ORM\ManyToOne(targetEntity: Echelle::class, inversedBy: "echelles_etalonnage")]
    public Echelle $echelle;

    #[ORM\ManyToOne(targetEntity: Etalonnage::class, inversedBy: "echelles")]
    public Etalonnage $etalonnage;

    /**
     * @param int $id
     * @param array $bounds
     * @param Echelle $echelle
     * @param Etalonnage $etalonnage
     */
    public function __construct(int $id, array $bounds, Echelle $echelle, Etalonnage $etalonnage)
    {
        $this->id = $id;
        $this->bounds = $bounds;
        $this->echelle = $echelle;
        $this->etalonnage = $etalonnage;
    }


}