<?php

namespace App\Form\Data;

use App\Entity\EchelleGraphique;

class EchelleSubtestBrMr
{

    public EchelleGraphique $echelle_br;
    public EchelleGraphique $echelle_mr;

    /**
     * @param EchelleGraphique $echelle_br
     * @param EchelleGraphique $echelle_mr
     */
    public function __construct(EchelleGraphique $echelle_br, EchelleGraphique $echelle_mr)
    {
        $this->echelle_br = $echelle_br;
        $this->echelle_mr = $echelle_mr;
    }


}