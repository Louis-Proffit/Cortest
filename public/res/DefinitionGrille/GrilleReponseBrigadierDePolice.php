<?php

namespace Res\DefinitionGrille;

use App\Core\Entities\GrilleReponse;

class GrilleReponseBrigadierDePolice extends GrilleReponse
{
    public int $numero_de_candidat;
    public int $sgap;

    public function fill(array $raw)
    {
        $this->numero_de_candidat = $this->getOrDefault("numero_candidat", $raw, 0);
        $this->sgap = $this->getOrDefault("sgap", $raw, 0);
        $this->reponses = $this->getOrDefault("reponses", $raw, array());
    }
}
