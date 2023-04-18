<?php

namespace App\Form\Data;

class EtalonnageGaussienCreer
{

    public array $echelleEtalonnageGaussienCreer;

    /**
     * @param array $echelleEtalonnageGaussienCreer
     */
    public function __construct(array $echelleEtalonnageGaussienCreer)
    {
        $this->echelleEtalonnageGaussienCreer = $echelleEtalonnageGaussienCreer;
    }
}