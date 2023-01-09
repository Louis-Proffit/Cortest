<?php

namespace App\Core\Res;

class EtalonnageRow
{
    public array $values;

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }
}