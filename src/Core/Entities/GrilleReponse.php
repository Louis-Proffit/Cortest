<?php

namespace App\Core\Entities;

/**
 * Données fournies par un candidat sur sa feuille de réponses
 */
abstract class GrilleReponse
{
    /**
     * @var array Le champ réponses est commun à toutes les grilles, bien que sa longueur puisse différer d'une grille à l'autre.
     */
    public array $reponses;

    /**
     * Consomme les données du lecteur optique, concaténées dans l'argument raw.
     */
    abstract function fill(array $raw);

    /**
     * @template T
     * @param string $key
     * @param T[] $raw
     * @param T $default
     * @return T
     */
    function getOrDefault(string $key, array $raw, $default)
    {
        return array_key_exists($key, $raw) ? $raw[$key] : $default;
    }
}