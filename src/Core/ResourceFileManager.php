<?php

namespace App\Core;

/**
 * Gère les fichiers liés aux entités resource.
 * Les fichiers sont stockés dans un dossier configuré dans les paramètres de l'application (var/res par exemple).
 * Ils sont stockés avec pour nom l'identifiant de la resource.
 * Leur création et suppression est la responsabilité du controller.
 * @template-extends CortestFileManager<Resource>
 */
readonly class ResourceFileManager extends CortestFileManager
{

    protected function entityFileName(mixed $entity): string
    {
        return $entity->id;
    }
}