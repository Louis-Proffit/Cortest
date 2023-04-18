<?php

namespace App\Form\Data;

use App\Entity\EchelleEtalonnage;
use Symfony\Component\Validator\Constraints as Assert;

class EchelleEtalonnageGaussienCreer
{

    public EchelleEtalonnage $echelleEtalonnage;
    public float $mean;
    #[Assert\NotNull]
    public float $stdDev;

    /**
     * @param EchelleEtalonnage $echelleEtalonnage
     * @param float $mean
     * @param float $stdDev
     */
    public function __construct(EchelleEtalonnage $echelleEtalonnage, float $mean, float $stdDev)
    {
        $this->echelleEtalonnage = $echelleEtalonnage;
        $this->mean = $mean;
        $this->stdDev = $stdDev;
    }
}