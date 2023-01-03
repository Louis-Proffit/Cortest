<?php

namespace Res\DefinitionGrille;

use App\Core\Entities\GrilleReponse;

class GrilleReponseBrigadierDePolice extends GrilleReponse
{
    public int $numero_de_candidat;
    public int $sgap;

    public function fill(string $raw)
    {
        $this->numero_de_candidat = 10;
        $this->sgap = 5;
        $this->reponses = array_fill(1, 120, '@');
    }
}
