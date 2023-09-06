<?php

namespace App\Core\ReponseCandidat;

use App\Form\Data\ParametresRecherche;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @extends AbstractItemStorage<RechercheFiltre>
 *
 * @method ParametresRecherche get()
 * @method ParametresRecherche getOrSetDefault(ParametresRecherche $default)
 * @method set(ParametresRecherche $item)
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