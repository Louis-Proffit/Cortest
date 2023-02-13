<?php

namespace App\Recherche;

use App\Form\Data\RechercheFiltre;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @extends AbstractItemStorage<RechercheFiltre>
 *
 * @method RechercheFiltre get()
 * @method RechercheFiltre getOrSetDefault(RechercheFiltre $default)
 * @method set(RechercheFiltre $item)
 */
class FiltreSessionStorage extends AbstractItemStorage
{

    const KEY = "recherche_filtre_key";

    public function __construct(
        private readonly RequestStack $session
    )
    {
        parent::__construct($this->session, self::KEY);
    }


}