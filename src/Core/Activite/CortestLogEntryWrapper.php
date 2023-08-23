<?php

namespace App\Core\Activite;

use App\Entity\CortestLogEntry;

class CortestLogEntryWrapper
{

    public CortestLogEntry $log;
    public string|null $lien;
    public object|null $object;

    /**
     * @param CortestLogEntry $log
     * @param string|null $lien
     * @param object|null $object
     */
    public function __construct(CortestLogEntry $log, ?string $lien, ?object $object)
    {
        $this->log = $log;
        $this->lien = $lien;
        $this->object = $object;
    }
}