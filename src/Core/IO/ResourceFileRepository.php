<?php

namespace App\Core\IO;

use App\Entity\Resource;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Gère les fichiers liés aux entités resource.
 * Les fichiers sont stockés dans un dossier configuré dans les paramètres de l'application (var/res par exemple).
 * Ils sont stockés avec pour nom l'identifiant de la resource.
 * Leur création et suppression est la responsabilité du controller.
 * @method entityFilePathOrNull(Resource $entity)
 * @method entityFilePath(Resource $entity)
 * @method upload(UploadedFile $file, Resource $entity)
 * @method delete(Resource $entity)
 */
readonly class ResourceFileRepository extends AbstractFileRepository
{

    protected function entityFileName(mixed $entity): string
    {
        return $entity->id;
    }
}