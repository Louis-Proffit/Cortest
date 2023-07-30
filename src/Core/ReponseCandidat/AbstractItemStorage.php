<?php

namespace App\Core\ReponseCandidat;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @template T
 */
readonly class AbstractItemStorage
{

    public function __construct(
        private RequestStack $requestStack,
        private string       $key
    )
    {
    }

    /**
     * @param T $item
     * @return void
     */
    public function set($item): void
    {
        $this->requestStack->getSession()->set($this->key, $item);
    }

    public function has(): bool
    {
        return $this->requestStack->getSession()->has($this->key);
    }

    /**
     * @return T
     */
    public function getOrSetDefault($default): mixed
    {
        if ($this->has()) {
            return $this->get();
        } else {
            $this->set($default);
            return $default;
        }
    }

    /**
     * @return T
     */
    public function get(): mixed
    {
        return $this->requestStack->getSession()->get($this->key);
    }

}