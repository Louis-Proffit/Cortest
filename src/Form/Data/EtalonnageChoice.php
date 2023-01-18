<?php

namespace App\Form\Data;

use App\Entity\Etalonnage;

class EtalonnageChoice
{
    public Etalonnage $etalonnage;

    /**
     * @param Etalonnage $etalonnage
     */
    public function __construct(Etalonnage $etalonnage)
    {
        $this->etalonnage = $etalonnage;
    }


}