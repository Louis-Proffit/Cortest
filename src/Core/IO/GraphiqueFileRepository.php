<?php

namespace App\Core\IO;

use App\Entity\Graphique;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @method entityFilePathOrNull(Graphique $entity)
 * @method entityFilePath(Graphique $entity)
 * @method upload(UploadedFile $file, Graphique $entity)
 * @method delete(Graphique $entity)
 */
readonly class GraphiqueFileRepository extends AbstractFileRepository
{


    protected function entityFileName(mixed $entity): string
    {
        return $entity->id;
    }
}