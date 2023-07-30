<?php

namespace App\Core\ReponseCandidat;

use Symfony\Component\HttpFoundation\RequestStack;


/**
 * @extends AbstractItemStorage<App\Recherche\ReponseCandidat[]>
 *
 * @method int[] get()
 * @method int[] getOrSetDefault(int[] $default)
 * @method set(int[] $item)
 */
readonly class ReponsesCandidatSessionStorage extends AbstractItemStorage
{

    const KEY = "reponses_candidat_key";

    public function __construct(
        private RequestStack $requestStack
    )
    {
        parent::__construct($this->requestStack, self::KEY);
    }
}