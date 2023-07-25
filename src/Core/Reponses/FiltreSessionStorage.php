<?php

namespace App\Core\Reponses;

use App\Form\Data\RechercheParameters;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @extends AbstractItemStorage<RechercheFiltre>
 *
 * @method RechercheParameters get()
 * @method RechercheParameters getOrSetDefault(RechercheParameters $default)
 * @method set(RechercheParameters $item)
 */
class FiltreSessionStorage extends AbstractItemStorage
{

    const KEY = "recherche_filtre_key";

    public function __construct(
        private readonly RequestStack $requestStack
    )
    {
        parent::__construct($this->requestStack, self::KEY);
    }


}