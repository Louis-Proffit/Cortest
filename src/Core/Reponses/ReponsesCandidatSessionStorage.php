<?php

namespace App\Core\Reponses;

use Symfony\Component\HttpFoundation\RequestStack;


/**
 * @extends AbstractItemStorage<App\Recherche\ReponseCandidat[]>
 *
 * @method int[] get()
 * @method int[] getOrSetDefault(int[] $default)
 * @method set(int[] $item)
 */
class ReponsesCandidatSessionStorage extends AbstractItemStorage
{

    const KEY = "reponses_candidat_key";

    public function __construct(
        private readonly RequestStack $requestStack
    )
    {
        parent::__construct($this->requestStack, self::KEY);
    }
}