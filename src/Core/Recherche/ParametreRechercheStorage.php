<?php

namespace App\Core\Recherche;

use App\Core\ReponseCandidat\AbstractItemStorage;
use App\Form\Data\ParametresRecherche;
use Symfony\Component\HttpFoundation\RequestStack;


/**
 * @method ParametresRecherche|null get()
 * @method void set($item)
 * @method bool has()
 * @method ParametresRecherche getOrSetDefault($default)
 */
readonly class ParametreRechercheStorage extends AbstractItemStorage
{

    const KEY = "parametre_recherche";

    public function __construct(RequestStack $requestStack)
    {
        parent::__construct($requestStack, self::KEY);
    }

}