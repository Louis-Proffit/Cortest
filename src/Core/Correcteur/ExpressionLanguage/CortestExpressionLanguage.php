<?php

namespace App\Core\Correcteur\ExpressionLanguage;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class CortestExpressionLanguage extends ExpressionLanguage
{

    public function __construct(CacheItemPoolInterface $cache = null, array $providers = [])
    {

        array_unshift($providers, new CortestExpressionFunctionProvider());

        parent::__construct($cache, $providers);
    }

}