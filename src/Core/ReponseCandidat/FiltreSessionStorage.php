<?php

namespace App\Core\ReponseCandidat;

use App\Form\Data\RechercheParameters;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @extends AbstractItemStorage<RechercheFiltre>
 *
 * @method RechercheParameters get()
 * @method RechercheParameters getOrSetDefault(RechercheParameters $default)
 * @method set(RechercheParameters $item)
 */
readonly class FiltreSessionStorage extends AbstractItemStorage
{

    const KEY = "recherche_filtre_key";

    public function __construct(
        private RequestStack $requestStack
    )
    {
        parent::__construct($this->requestStack, self::KEY);
    }


}