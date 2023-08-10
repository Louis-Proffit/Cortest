<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Expression;
use Symfony\Component\Validator\Constraints\Type;

#[Gedmo\Loggable()]
#[Entity]
class EchelleEtalonnage
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[Expression("this.etalonnage.nombre_classes == count(this.bounds)")]
    #[All(new Type("numeric"))]
    #[Gedmo\Versioned]
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

    public static function rangeEchelle(Echelle $echelle, Etalonnage $etalonnage, int $nombreClasses): EchelleEtalonnage
    {
        $bounds = [];

        for ($i =1; $i <= $nombreClasses - 1; $i++) {
            $bounds[] = $i;
        }

        return new EchelleEtalonnage(
            id: 0,
            bounds: $bounds,
            echelle: $echelle,
            etalonnage: $etalonnage,
        );
    }


}