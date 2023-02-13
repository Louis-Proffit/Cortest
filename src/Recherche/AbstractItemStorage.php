<?php

namespace App\Recherche;

use App\Form\Data\RechercheFiltre;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @template T
 */
class AbstractItemStorage
{

    public function __construct(
        private readonly RequestStack $session,
        private readonly string           $key
    )
    {
    }

    /**
     * @param T $item
     * @return void
     */
    public function set($item): void
    {
        $this->session->getSession()->set($this->key, $item);
    }

    public function has(): bool
    {
        return $this->session->getSession()->has($this->key);
    }

    /**
     * @return T
     */
    public function getOrDefault($default): mixed
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
        return $this->session->getSession()->get($this->key);
    }

}