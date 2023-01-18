<?php

namespace App\Form\Data;

use App\Entity\Graphique;

class GraphiqueChoice
{
    public Graphique $graphique;

    /**
     * @param Graphique $graphique
     */
    public function __construct(Graphique $graphique)
    {
        $this->graphique = $graphique;
    }


}