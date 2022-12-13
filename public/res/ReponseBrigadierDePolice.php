<?php

namespace Res;

use App\Core\Entities\Reponse;

class ReponseBrigadierDePolice extends Reponse
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
