<?php

namespace App\Core\Entities;

use JMS\Serializer\Annotation\Type;

/**
 * Données fournies par un candidat sur sa feuille de réponses
 */
abstract class Reponse
{
    /**
     * @var array Le champ réponses est commun à toutes les grilles, bien que sa longueur puisse différer d'une grille à l'autre.
     */
    #[Type("array")]
    public array $reponses;

    /**
     * Consomme les données du lecteur optique, concaténées dans l'argument raw.
     */
    abstract function fill(string $raw);
}