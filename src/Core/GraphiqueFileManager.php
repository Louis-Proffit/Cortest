<?php

namespace App\Core;

/**
 * @template-extends CortestFileManager<Graphique>
 */
readonly class GraphiqueFileManager extends CortestFileManager
{


    protected function entityFileName(mixed $entity): string
    {
        return $entity->id;
    }
}